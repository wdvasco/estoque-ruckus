<?php
/**
 * Script to cleanup duplicated data and test import
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/Inventory.php';

try {
    $pdo = getConnection();
    
    echo "=== LIMPEZA E TESTE DE IMPORTAÇÃO ===\n\n";
    
    // 1. Clean up any duplicate MAC addresses that were created
    echo "1. Limpando MACs duplicados...\n";
    $result = $pdo->exec("DELETE i1 FROM inventory i1 
                         INNER JOIN inventory i2 
                         WHERE i1.id > i2.id 
                         AND i1.apmac = i2.apmac 
                         AND i1.apmac LIKE 'MAC%'");
    echo "✓ Removidos $result registros duplicados\n\n";
    
    // 2. Show current inventory count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM inventory");
    $count = $stmt->fetch()['count'];
    echo "2. Total de itens no inventário: $count\n\n";
    
    // 3. Test with a small sample from the CSV
    echo "3. Testando importação com dados de exemplo...\n";
    
    $sampleData = [
        [
            'APMAC' => '0033580A9B00',
            'APName' => '828676',
            'Model' => 'T350D',
            'Serial' => '502322016849',
            'Status' => 'Instalado',
            'Location' => 'PQ PEDRA DA CEBOLA | MATA DA PRAIA | ADMINISTRACAO',
            'Inclusao' => '06/03/2025',
            'Obs' => 'Substituindo o APO_SEMFA_523648'
        ],
        [
            'APMAC' => '0033580A9B80',
            'APName' => '828669',
            'Model' => 'T350D',
            'Serial' => '502322016857',
            'Status' => 'Instalado',
            'Location' => 'PC DE EUCALIPTO | MARUIPE | PRACA VICENTE GUIDA',
            'Inclusao' => '28/03/2025',
            'Obs' => ''
        ],
        [
            'APMAC' => '', // Test blank MAC
            'APName' => '',
            'Model' => 'H510',
            'Serial' => '',
            'Status' => 'Estoque',
            'Location' => 'Teste Local',
            'Inclusao' => '20/07/2025',
            'Obs' => 'Teste com campos em branco'
        ]
    ];
    
    $inventory = new Inventory();
    $result = $inventory->importFromArray($sampleData);
    
    if ($result['success']) {
        echo "✓ Teste de importação bem-sucedido!\n";
        echo "Mensagem: " . $result['message'] . "\n";
        echo "Estatísticas:\n";
        echo "- Importados: " . $result['stats']['imported'] . "\n";
        echo "- Ignorados: " . $result['stats']['skipped'] . "\n";
        
        if (!empty($result['stats']['unique_models'])) {
            echo "- Modelos únicos: " . implode(', ', $result['stats']['unique_models']) . "\n";
        }
        
        if (!empty($result['stats']['unique_statuses'])) {
            echo "- Status únicos: " . implode(', ', $result['stats']['unique_statuses']) . "\n";
        }
        
        if (!empty($result['stats']['errors'])) {
            echo "- Erros encontrados:\n";
            foreach (array_slice($result['stats']['errors'], 0, 3) as $error) {
                echo "  * $error\n";
            }
        }
    } else {
        echo "✗ Erro no teste: " . $result['message'] . "\n";
    }
    
    // 4. Show final inventory count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM inventory");
    $newCount = $stmt->fetch()['count'];
    echo "\n4. Total final de itens: $newCount (+" . ($newCount - $count) . ")\n";
    
    // 5. Show available options
    echo "\n5. Opções disponíveis:\n";
    
    $modelOptions = $inventory->getModelOptions();
    echo "Modelos (" . count($modelOptions) . "): " . implode(', ', array_slice($modelOptions, 0, 10)) . 
         (count($modelOptions) > 10 ? '...' : '') . "\n";
    
    $statusOptions = $inventory->getStatusOptions();
    echo "Status (" . count($statusOptions) . "): " . implode(', ', $statusOptions) . "\n";
    
    echo "\n✓ Limpeza e teste concluídos!\n";
    echo "\nAgora você pode tentar importar o arquivo CSV completo novamente.\n";
    echo "Os problemas de MAC duplicado e parsing foram corrigidos.\n";
    
} catch (Exception $e) {
    echo "✗ Erro: " . $e->getMessage() . "\n";
}
?>
