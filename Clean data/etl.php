<?php
// Minimal PHP ETL for Customers.xlsx and Links.xlsx
// Dependencies: phpoffice/phpspreadsheet (Composer)
// Usage:
//   composer require phpoffice/phpspreadsheet
//   php etl.php [--no-db]
// Env (optional for MySQL): MYSQL_ENABLED=1 MYSQL_HOST=localhost MYSQL_DB=clean_db MYSQL_USER=root MYSQL_PASSWORD=secret

$autoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    fwrite(STDERR, "Missing vendor/autoload.php. Install with Composer: composer require phpoffice/phpspreadsheet\n");
    exit(1);
}
require $autoload;

use PhpOffice\PhpSpreadsheet\IOFactory;

function normalize($v) {
    if ($v === null) return '';
    if (is_numeric($v)) return trim((string)$v);
    return trim((string)$v);
}

function loadExcelRows(string $path, array $requiredKeys): array {
    if (!file_exists($path)) throw new RuntimeException("File not found: $path");
    $spreadsheet = IOFactory::load($path);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray(null, true, true, true); // keys A,B,C...
    // Find header row (first non-empty row)
    $headerRow = null; $map = [];
    foreach ($rows as $idx => $r) {
        $values = array_values($r);
        $joined = implode('', array_map(fn($x)=>normalize($x), $values));
        if ($joined !== '') { $headerRow = $idx; break; }
    }
    if ($headerRow === null) return [];
    $headerVals = array_map(fn($x)=>strtolower(normalize($x)), $rows[$headerRow]);
    foreach ($requiredKeys as $key) {
        $foundCol = null; $i=0;
        foreach ($headerVals as $col => $name) { if ($name === $key) { $foundCol = $col; break; } $i++; }
        if ($foundCol === null) throw new RuntimeException("Missing required column '$key' in $path");
        $map[$key] = $foundCol;
    }
    $data = [];
    foreach ($rows as $idx => $r) {
        if ($idx <= $headerRow) continue;
        $obj = [];
        $empty = true;
        foreach ($requiredKeys as $key) { $val = normalize($r[$map[$key]] ?? null); $obj[$key] = $val; if ($val !== '') $empty = false; }
        if (!$empty) $data[] = $obj;
    }
    return $data;
}

function loadCustomers(string $baseDir): array {
    $path = $baseDir . DIRECTORY_SEPARATOR . 'Customers.xlsx';
    $rows = loadExcelRows($path, ['account_number','contract_number','customer','address']);
    return $rows;
}

function loadLinks(string $baseDir): array {
    $path = $baseDir . DIRECTORY_SEPARATOR . 'Links.xlsx';
    $rows = loadExcelRows($path, ['account_number','customer','link','sapcodes']);
    return $rows;
}

function connectMysql(): ?PDO {
    $enabled = getenv('MYSQL_ENABLED');
    if ($enabled === false) $enabled = '1';
    if ($enabled === '0') return null;
    $host = getenv('MYSQL_HOST') ?: 'localhost';
    $db   = getenv('MYSQL_DB') ?: 'billing'; // default to 'billing' per request
    $user = getenv('MYSQL_USER') ?: 'root';
    $pass = getenv('MYSQL_PASSWORD') ?: '';
    $options = [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION];
    try {
        $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        $adminDsn = "mysql:host=$host;charset=utf8mb4";
        $pdoAdmin = new PDO($adminDsn, $user, $pass, $options);
        $pdoAdmin->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
        return new PDO($dsn, $user, $pass, $options);
    }
}

function findCustomerId(PDO $pdo, string $accountNumber): ?int {
    $stmt = $pdo->prepare("SELECT id FROM customers WHERE account_number = :acc LIMIT 1");
    $stmt->execute([':acc' => $accountNumber]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (int)$row['id'] : null;
}

function upsertCustomers(PDO $pdo, array $rows): void {
    // Insert or update customers based on account_number, set timestamps
    $select = $pdo->prepare("SELECT id FROM customers WHERE account_number = :acc LIMIT 1");
    $insert = $pdo->prepare("INSERT INTO customers (account_manager_id, account_number, contract_number, address, contact_number, customer, created_at, updated_at)
                             VALUES (NULL, :acc, :contract, :addr, :contact, :cust, NOW(), NOW())");
    $update = $pdo->prepare("UPDATE customers SET contract_number = :contract, address = :addr, contact_number = :contact, customer = :cust, updated_at = NOW() WHERE id = :id");
    foreach ($rows as $r) {
        $acc = $r['account_number'] ?? '';
        if ($acc === '') continue;
        $select->execute([':acc' => $acc]);
        $found = $select->fetch(PDO::FETCH_ASSOC);
        $contract = $r['contract_number'] ?? null;
        $addr = $r['address'] ?? null;
        $contact = $r['contact_number'] ?? null; // may be missing in Excel
        $cust = $r['customer'] ?? '';
        if ($found) {
            $update->execute([':contract'=>$contract, ':addr'=>$addr, ':contact'=>$contact, ':cust'=>$cust, ':id'=>$found['id']]);
        } else {
            $insert->execute([':acc'=>$acc, ':contract'=>$contract, ':addr'=>$addr, ':contact'=>$contact, ':cust'=>$cust]);
        }
    }
}

function upsertLinks(PDO $pdo, array $rows): void {
    // Upsert links keyed by (customer_id, link)
    $select = $pdo->prepare("SELECT id FROM links WHERE customer_id = :cust_id AND link = :link LIMIT 1");
    $insert = $pdo->prepare("INSERT INTO links (customer_id, account_number, contract_number, jcc_number, sapcodes, comment, quantity, service_type, capacity, city_id, suburb_id, pop_id, linkType_id, link_status, link, created_at, updated_at)
                             VALUES (:cust_id, :acc, :contract, :jcc, :sap, :comment, :qty, :service, :capacity, :city, :suburb, :pop, :linkType, :status, :link, NOW(), NOW())");
    $update = $pdo->prepare("UPDATE links SET account_number=:acc, contract_number=:contract, jcc_number=:jcc, sapcodes=:sap, comment=:comment, quantity=:qty, service_type=:service, capacity=:capacity, city_id=:city, suburb_id=:suburb, pop_id=:pop, linkType_id=:linkType, link_status=:status, updated_at=NOW() WHERE id=:id");
    foreach ($rows as $r) {
        $acc = $r['account_number'] ?? '';
        $link = $r['link'] ?? '';
        if ($acc === '' || $link === '') continue;
        $custId = findCustomerId($pdo, $acc);
        if ($custId === null) continue; // avoid orphaned link
        $select->execute([':cust_id' => $custId, ':link' => $link]);
        $found = $select->fetch(PDO::FETCH_ASSOC);
        $common = [
            'acc' => $acc,
            'contract' => null,
            'jcc' => null,
            'sap' => $r['sapcodes'] ?? null,
            'comment' => null,
            'qty' => null,
            'service' => null,
            'capacity' => null,
            'city' => 0,
            'suburb' => 0,
            'pop' => 0,
            'linkType' => 0,
            'status' => 0,
        ];
        if ($found) {
            $update->execute([
                ':acc' => $common['acc'],
                ':contract' => $common['contract'],
                ':jcc' => $common['jcc'],
                ':sap' => $common['sap'],
                ':comment' => $common['comment'],
                ':qty' => $common['qty'],
                ':service' => $common['service'],
                ':capacity' => $common['capacity'],
                ':city' => $common['city'],
                ':suburb' => $common['suburb'],
                ':pop' => $common['pop'],
                ':linkType' => $common['linkType'],
                ':status' => $common['status'],
                ':id' => (int)$found['id'],
            ]);
        } else {
            $insert->execute([
                ':cust_id' => $custId,
                ':acc' => $common['acc'],
                ':contract' => $common['contract'],
                ':jcc' => $common['jcc'],
                ':sap' => $common['sap'],
                ':comment' => $common['comment'],
                ':qty' => $common['qty'],
                ':service' => $common['service'],
                ':capacity' => $common['capacity'],
                ':city' => $common['city'],
                ':suburb' => $common['suburb'],
                ':pop' => $common['pop'],
                ':linkType' => $common['linkType'],
                ':status' => $common['status'],
                ':link' => $link,
            ]);
        }
    }
}
// Ensure tables exist with appropriate keys
function ensureTables(PDO $pdo): void {
    $pdo->exec("CREATE TABLE IF NOT EXISTS customers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        account_number VARCHAR(64) NOT NULL,
        contract_number VARCHAR(64) DEFAULT NULL,
        customer VARCHAR(255) DEFAULT NULL,
        address VARCHAR(255) DEFAULT NULL,
        UNIQUE KEY uk_customers_account (account_number),
        UNIQUE KEY uk_customers_contract (contract_number)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $pdo->exec("CREATE TABLE IF NOT EXISTS links (
        id INT AUTO_INCREMENT PRIMARY KEY,
        account_number VARCHAR(64) NOT NULL,
        customer VARCHAR(255) DEFAULT NULL,
        link VARCHAR(255) NOT NULL,
        sapcodes VARCHAR(255) DEFAULT NULL,
        UNIQUE KEY uk_links_account_link (account_number, link)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
}

function main(array $argv): void {
    $base = __DIR__;
    $noDb = in_array('--no-db', $argv, true);
    $customers = loadCustomers($base);
    $links = loadLinks($base);
    echo "Customers loaded: ".count($customers)."\n";
    echo "Links rows: ".count($links)."\n";
    if ($noDb) { echo "DB disabled via flag. Done.\n"; return; }
    $pdo = connectMysql();
    if ($pdo === null) { echo "MYSQL_ENABLED=0; skipping DB. Done.\n"; return; }
    // assume schema exists per provided definitions
    upsertCustomers($pdo, $customers);
    upsertLinks($pdo, $links);
    echo "MySQL upsert complete. Done.\n";
}

main($argv);