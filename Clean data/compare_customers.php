<?php

$autoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoload)) { fwrite(STDERR, "Missing vendor/autoload.php\n"); exit(1); }
require $autoload;

use PhpOffice\PhpSpreadsheet\IOFactory;

function norm($v) { if ($v === null) return ''; if (is_numeric($v)) return trim((string)$v); return trim((string)$v); }

function normKey(string $v): string {
    $v = strtolower(norm($v));
    $v = preg_replace('/\s+/', '_', $v);
    $v = preg_replace('/[^a-z0-9_]+/', '_', $v);
    $v = preg_replace('/_+/', '_', $v);
    return trim($v, '_');
}

function resolveInputFile(string $base, array $candidates): string {
    foreach ($candidates as $name) {
        $path = $base . DIRECTORY_SEPARATOR . $name;
        if (is_file($path)) return $path;
    }

    $files = array_merge(
        glob($base . DIRECTORY_SEPARATOR . '*.xlsx') ?: [],
        glob($base . DIRECTORY_SEPARATOR . '*.xls') ?: [],
        glob($base . DIRECTORY_SEPARATOR . '*.csv') ?: []
    );
    $files = array_values(array_filter($files, fn($p) => basename($p) !== '' && substr(basename($p), 0, 2) !== '~$'));

    throw new RuntimeException(
        "Input file not found.\nTried: " . implode(', ', $candidates) . "\nFound in folder:\n- " . implode("\n- ", array_map('basename', $files))
    );
}

function getFirst(array $row, array $keys): string {
    foreach ($keys as $k) {
        if (isset($row[$k])) {
            $v = norm($row[$k]);
            if ($v !== '') return $v;
        }
    }
    return '';
}

function readSheet(string $path): array {
    if (!is_file($path)) throw new RuntimeException("File not found: $path");
    $spreadsheet = IOFactory::load($path);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray(null, true, true, true);
    $headerRow = null;
    foreach ($rows as $idx => $r) { $joined = implode('', array_map('norm', array_values($r))); if ($joined !== '') { $headerRow = $idx; break; } }
    if ($headerRow === null) return [];
    $header = [];
    foreach ($rows[$headerRow] as $col => $name) {
        $n = normKey((string)$name);
        if ($n !== '') $header[$col] = $n;
    }
    $data = [];
    foreach ($rows as $idx => $r) {
        if ($idx <= $headerRow) continue;
        $obj = [];
        $empty = true;
        foreach ($header as $col => $key) { $val = norm($r[$col] ?? null); $obj[$key] = $val; if ($val !== '') $empty = false; }
        if (!$empty) $data[] = $obj;
    }
    return $data;
}

function main(): void {
    $base = __DIR__;
    $argv = $_SERVER['argv'] ?? [];
    $in1 = $argv[1] ?? '';
    $in2 = $argv[2] ?? '';

    $p1 = $in1 !== '' ? $in1 : resolveInputFile($base, ['Customers.xlsx', 'Customers.csv']);
    $p2 = $in2 !== '' ? $in2 : resolveInputFile($base, ['Customers2.xlsx', 'Customers2.csv']);

    $c1 = readSheet($p1);
    $c2 = readSheet($p2);
    $set = [];
    foreach ($c1 as $r) {
        $acc = getFirst($r, ['account_number', 'accountnumber', 'account']);
        if ($acc !== '') $set[$acc] = true;
    }
    $out = [];
    foreach ($c2 as $r) {
        $acc = getFirst($r, ['account_number', 'accountnumber', 'account']);
        if ($acc === '') continue;
        if (!isset($set[$acc])) {
            $contract = getFirst($r, ['contract_number', 'contractnumber', 'contract_nunber']);
            $customer = getFirst($r, ['customer', 'cutstomer', 'custome']);
            $out[] = [$acc, $contract, $customer];
        }
    }
    $csv = $base . DIRECTORY_SEPARATOR . 'customers2_not_in_customers.csv';
    $fp = fopen($csv, 'w');
    foreach ($out as $row) { fputcsv($fp, $row); }
    fclose($fp);
}

main();
