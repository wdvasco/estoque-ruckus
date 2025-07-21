<?php
/**
 * Test Flexible Import Functionality
 * Tests the new flexible import with blank fields
 */

require_once 'config/app.php';
require_once 'classes/Inventory.php';

echo "<h2>🧪 Teste de Importação Flexível</h2>";

try {
    $inventory = new Inventory();
    
    // Sample data with blank fields (simulating CSV with missing data)
    $testData = [
        // Row 1: Complete data
        [
            'APMAC' => '00:11:22:33:44:AA',
            'APName' => 'AP-TEST-01',
            'Model' => 'R750',
            'Serial' => 'TEST001',
            'Status' => 'Active',
            'Location' => 'Test Location 1',
            'Inclusao' => '2024-01-01',
            'Obs' => 'Test item with all fields'
        ],
        // Row 2: Missing MAC and Serial
        [
            'APMAC' => '',
            'APName' => 'AP-TEST-02',
            'Model' => 'R650',
            'Serial' => '',
            'Status' => 'Active',
            'Location' => 'Test Location 2',
            'Inclusao' => '2024-01-02',
            'Obs' => 'Test item with missing MAC and Serial'
        ],
        // Row 3: Only APName
        [
            'APMAC' => '',
            'APName' => 'AP-TEST-03',
            'Model' => '',
            'Serial' => '',
            'Status' => '',
            'Location' => '',
            'Inclusao' => '',
            'Obs' => 'Test item with only name'
        ],
        // Row 4: Completely blank except obs
        [
            'APMAC' => '',
            'APName' => '',
            'Model' => '',
            'Serial' => '',
            'Status' => '',
            'Location' => '',
            'Inclusao' => '',
            'Obs' => 'Test item mostly blank'
        ],
        // Row 5: Mixed data
        [
            'APMAC' => '00:11:22:33:44:BB',
            'APName' => '',
            'Model' => 'R550',
            'Serial' => 'TEST002',
            'Status' => 'Inactive',
            'Location' => '',
            'Inclusao' => '',
            'Obs' => ''
        ]
    ];
    
    echo "<h3>Dados de Teste:</h3>";
    echo "<pre>" . print_r($testData, true) . "</pre>";
    
    echo "<h3>Executando Importação Flexível:</h3>";
    $result = $inventory->importFromArray($testData);
    
    if ($result['success']) {
        echo "<div style='color: green;'>";
        echo "<p>✅ <strong>" . $result['message'] . "</strong></p>";
        echo "<p>📊 <strong>Estatísticas:</strong></p>";
        echo "<ul>";
        echo "<li>Importados: " . $result['stats']['imported'] . "</li>";
        echo "<li>Ignorados: " . $result['stats']['skipped'] . "</li>";
        echo "<li>Total de erros: " . count($result['stats']['errors']) . "</li>";
        echo "</ul>";
        
        if (!empty($result['stats']['errors'])) {
            echo "<p><strong>Erros encontrados:</strong></p>";
            echo "<ul>";
            foreach (array_slice($result['stats']['errors'], 0, 5) as $error) {
                echo "<li>" . htmlspecialchars($error) . "</li>";
            }
            if (count($result['stats']['errors']) > 5) {
                echo "<li>... e mais " . (count($result['stats']['errors']) - 5) . " erros</li>";
            }
            echo "</ul>";
        }
        echo "</div>";
    } else {
        echo "<div style='color: red;'>";
        echo "<p>❌ <strong>Erro na importação:</strong> " . $result['message'] . "</p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red;'>";
    echo "<p>❌ <strong>Erro:</strong> " . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<hr>";
echo "<h3>📋 Instruções:</h3>";
echo "<ol>";
echo "<li><strong>Execute o script SQL:</strong> <code>database/update_flexible_import.sql</code> para atualizar o banco</li>";
echo "<li><strong>Teste a importação</strong> com sua planilha CSV</li>";
echo "<li><strong>Campos em branco</strong> receberão valores padrão automaticamente</li>";
echo "<li><strong>Duplicatas</strong> serão ignoradas ao invés de causar erro</li>";
echo "</ol>";

echo "<p><a href='dashboard/import.php'>📥 Ir para Importação</a></p>";
echo "<p><a href='dashboard/index.php'>🏠 Voltar ao Dashboard</a></p>";
?>
