<?php
/**
 * CSV Analysis Script
 * Analyzes the provided CSV file to extract unique models and statuses
 */

require_once 'config/database.php';

$csvFile = 'Gestão de APs.CSV';

if (!file_exists($csvFile)) {
    die("Arquivo CSV não encontrado: $csvFile\n");
}

// Read and parse CSV file
$handle = fopen($csvFile, 'r');
if (!$handle) {
    die("Erro ao abrir o arquivo CSV\n");
}

// Skip header row
$header = fgetcsv($handle, 1000, ';');
echo "Cabeçalho encontrado: " . implode(', ', $header) . "\n\n";

$uniqueModels = [];
$uniqueStatuses = [];
$totalRows = 0;

// Process each row
while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
    $totalRows++;
    
    // Map columns based on header
    $row = [
        'APMAC' => $data[0] ?? '',
        'APName' => $data[1] ?? '',
        'Model' => $data[2] ?? '',
        'Serial' => $data[3] ?? '',
        'Status' => $data[4] ?? '',
        'Location' => $data[5] ?? '',
        'Inclusao' => $data[6] ?? '',
        'Obs' => $data[7] ?? ''
    ];
    
    // Collect unique models
    if (!empty($row['Model']) && !in_array($row['Model'], $uniqueModels)) {
        $uniqueModels[] = $row['Model'];
    }
    
    // Collect unique statuses
    if (!empty($row['Status']) && !in_array($row['Status'], $uniqueStatuses)) {
        $uniqueStatuses[] = $row['Status'];
    }
}

fclose($handle);

// Sort arrays
sort($uniqueModels);
sort($uniqueStatuses);

// Display results
echo "=== ANÁLISE DO ARQUIVO CSV ===\n";
echo "Total de linhas processadas: $totalRows\n\n";

echo "MODELOS ÚNICOS ENCONTRADOS (" . count($uniqueModels) . "):\n";
foreach ($uniqueModels as $model) {
    echo "- $model\n";
}

echo "\n";
echo "STATUS ÚNICOS ENCONTRADOS (" . count($uniqueStatuses) . "):\n";
foreach ($uniqueStatuses as $status) {
    echo "- $status\n";
}
echo "\n";

// Generate SQL for inserting options
echo "\n=== SQL PARA INSERIR OPÇÕES ===\n";
echo "-- Inserir modelos únicos\n";
foreach ($uniqueModels as $model) {
    echo "INSERT IGNORE INTO inventory_options (option_type, option_value) VALUES ('model', '" . addslashes($model) . "');\n";
}

echo "\n-- Inserir status únicos\n";
foreach ($uniqueStatuses as $status) {
    echo "INSERT IGNORE INTO inventory_options (option_type, option_value) VALUES ('status', '" . addslashes($status) . "');\n";
}

// Show sample data format
echo "\n=== EXEMPLO DE FORMATAÇÃO ===\n";
$handle = fopen($csvFile, 'r');
fgetcsv($handle, 1000, ';'); // Skip header

$sampleCount = 0;
while (($data = fgetcsv($handle, 1000, ';')) !== FALSE && $sampleCount < 3) {
    $sampleCount++;
    
    echo "Linha $sampleCount:\n";
    echo "  APMAC Original: {$data[0]}\n";
    echo "  APMAC Formatado: " . strtoupper(str_replace([':', '-', ' '], '', $data[0])) . "\n";
    echo "  Data Original: {$data[6]}\n";
    
    // Parse and format date
    $dateStr = $data[6];
    if (preg_match('/(\d{1,2})\/(\d{1,2})\/(\d{4})/', $dateStr, $matches)) {
        $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
        $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
        $year = $matches[3];
        echo "  Data Formatada: $day-$month-$year\n";
    }
    echo "\n";
}

fclose($handle);

echo "Análise concluída!\n";
?>
