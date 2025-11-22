<?php

$autoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoload)) { fwrite(STDERR, "Missing vendor/autoload.php\n"); exit(1); }
require $autoload;

use PhpOffice\PhpSpreadsheet\IOFactory;

function norm($v) { if ($v === null) return ''; if (is_numeric($v)) return trim((string)$v); return trim((string)$v); }

function readSheet(string $path): array {
    if (!file_exists($path)) throw new RuntimeException("File not found: $path");
    $spreadsheet = IOFactory::load($path);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray(null, true, true, true);
    $headerRow = null;
    foreach ($rows as $idx => $r) { $joined = implode('', array_map('norm', array_values($r))); if ($joined !== '') { $headerRow = $idx; break; } }
    if ($headerRow === null) return [];
    $header = [];
    foreach ($rows[$headerRow] as $col => $name) { $n = strtolower(norm($name)); if ($n !== '') $header[$col] = $n; }
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
    $c1 = readSheet($base . DIRECTORY_SEPARATOR . 'Customers.xlsx');
    $c2 = readSheet($base . DIRECTORY_SEPARATOR . 'Customers2.xlsx');
    $set = [];
    foreach ($c1 as $r) { $acc = $r['account_number'] ?? ''; if ($acc !== '') $set[$acc] = true; }
    $out = [];
    foreach ($c2 as $r) {
        $acc = $r['account_number'] ?? '';
        if ($acc === '') continue;
        if (!isset($set[$acc])) $out[] = [$acc, $r['contract_number'] ?? '', $r['customer'] ?? ''];
    }
    $csv = $base . DIRECTORY_SEPARATOR . 'customers2_not_in_customers.csv';
    $fp = fopen($csv, 'w');
    foreach ($out as $row) { fputcsv($fp, $row); }
    fclose($fp);
}

main();