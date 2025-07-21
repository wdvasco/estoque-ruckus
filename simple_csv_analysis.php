<?php
/**
 * Simple CSV Analysis Script
 */

$csvFile = 'Gestão de APs.CSV';

if (!file_exists($csvFile)) {
    die("Arquivo não encontrado\n");
}

$models = [];
$statuses = [];
$count = 0;

$handle = fopen($csvFile, 'r');
fgetcsv($handle, 1000, ';'); // Skip header

while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
    $count++;
    
    $model = trim($data[2] ?? '');
    $status = trim($data[4] ?? '');
    
    if (!empty($model) && !in_array($model, $models)) {
        $models[] = $model;
    }
    
    if (!empty($status) && !in_array($status, $statuses)) {
        $statuses[] = $status;
    }
}

fclose($handle);

sort($models);
sort($statuses);

echo "Total de linhas: $count\n\n";

echo "MODELOS (" . count($models) . "):\n";
foreach ($models as $model) {
    echo "$model\n";
}

echo "\nSTATUS (" . count($statuses) . "):\n";
foreach ($statuses as $status) {
    echo "$status\n";
}

echo "\nSQL para modelos:\n";
foreach ($models as $model) {
    echo "INSERT IGNORE INTO inventory_options (option_type, option_value) VALUES ('model', '$model');\n";
}

echo "\nSQL para status:\n";
foreach ($statuses as $status) {
    echo "INSERT IGNORE INTO inventory_options (option_type, option_value) VALUES ('status', '$status');\n";
}
?>
