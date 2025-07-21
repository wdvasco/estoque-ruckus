<?php
/**
 * Test script for dynamic options functionality
 */

require_once 'config/database.php';
require_once 'classes/Inventory.php';

echo "=== TESTE DE OPÇÕES DINÂMICAS ===\n\n";

// Test inventory class
$inventory = new Inventory();

// Test getting model options
echo "1. Testando getModelOptions():\n";
$modelOptions = $inventory->getModelOptions();
echo "Modelos encontrados (" . count($modelOptions) . "):\n";
foreach ($modelOptions as $model) {
    echo "- $model\n";
}

echo "\n2. Testando getStatusOptions():\n";
$statusOptions = $inventory->getStatusOptions();
echo "Status encontrados (" . count($statusOptions) . "):\n";
foreach ($statusOptions as $status) {
    echo "- $status\n";
}

// Test import with sample data
echo "\n3. Testando importação com dados de exemplo:\n";
$sampleData = [
    [
        'APMAC' => '0033580A9B00',
        'APName' => 'AP-TEST-001',
        'Model' => 'T350D',
        'Serial' => 'TEST123456',
        'Status' => 'Instalado',
        'Location' => 'Teste Local',
        'Inclusao' => '06/03/2025',
        'Obs' => 'Teste de importação'
    ],
    [
        'APMAC' => '184B0D125840',
        'APName' => 'AP-TEST-002',
        'Model' => 'H510',
        'Serial' => 'TEST789012',
        'Status' => 'Estoque',
        'Location' => 'Teste Local 2',
        'Inclusao' => '07/03/2025',
        'Obs' => 'Segundo teste'
    ]
];

$result = $inventory->importFromArray($sampleData);

if ($result['success']) {
    echo "✓ Importação bem-sucedida!\n";
    echo "Mensagem: " . $result['message'] . "\n";
    echo "Estatísticas:\n";
    echo "- Importados: " . $result['stats']['imported'] . "\n";
    echo "- Ignorados: " . $result['stats']['skipped'] . "\n";
    
    if (!empty($result['stats']['unique_models'])) {
        echo "- Modelos únicos encontrados: " . implode(', ', $result['stats']['unique_models']) . "\n";
    }
    
    if (!empty($result['stats']['unique_statuses'])) {
        echo "- Status únicos encontrados: " . implode(', ', $result['stats']['unique_statuses']) . "\n";
    }
} else {
    echo "✗ Erro na importação: " . $result['message'] . "\n";
}

// Test getting updated options after import
echo "\n4. Verificando opções após importação:\n";
$updatedModelOptions = $inventory->getModelOptions();
$updatedStatusOptions = $inventory->getStatusOptions();

echo "Modelos após importação (" . count($updatedModelOptions) . "):\n";
foreach ($updatedModelOptions as $model) {
    echo "- $model\n";
}

echo "\nStatus após importação (" . count($updatedStatusOptions) . "):\n";
foreach ($updatedStatusOptions as $status) {
    echo "- $status\n";
}

echo "\n=== TESTE CONCLUÍDO ===\n";
?>
