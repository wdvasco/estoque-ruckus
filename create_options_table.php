<?php
/**
 * Script to create options table directly via PHP
 */

require_once 'config/database.php';

try {
    $pdo = getConnection();
    
    echo "Criando tabela inventory_options...\n";
    
    // Create table
    $sql = "
    CREATE TABLE IF NOT EXISTS inventory_options (
        id INT AUTO_INCREMENT PRIMARY KEY,
        option_type ENUM('model', 'status') NOT NULL COMMENT 'Type of option',
        option_value VARCHAR(100) NOT NULL COMMENT 'The option value',
        is_active BOOLEAN DEFAULT TRUE COMMENT 'Whether option is active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        
        -- Ensure unique combinations
        UNIQUE KEY unique_option (option_type, option_value),
        
        -- Indexes for performance
        INDEX idx_type (option_type),
        INDEX idx_active (is_active)
    )";
    
    $pdo->exec($sql);
    echo "✓ Tabela criada com sucesso!\n\n";
    
    echo "Inserindo modelos...\n";
    
    // Insert models
    $models = ['H510', 'MODELOANTIGO', 'P300', 'R510', 'R550', 'R600', 'R650', 'T300', 'T310D', 'T350', 'T350D', 'T510', 'ZF7341', 'ZF7363', 'ZF7372', 'ZF7762'];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO inventory_options (option_type, option_value) VALUES ('model', ?)");
    
    foreach ($models as $model) {
        $stmt->execute([$model]);
        echo "- $model\n";
    }
    
    echo "\nInserindo status...\n";
    
    // Insert statuses
    $statuses = ['Desaparecido', 'Descarte', 'Estoque', 'Flagged', 'Instalado', 'Queimado', 'Verificar'];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO inventory_options (option_type, option_value) VALUES ('status', ?)");
    
    foreach ($statuses as $status) {
        $stmt->execute([$status]);
        echo "- $status\n";
    }
    
    echo "\n✓ Dados inseridos com sucesso!\n\n";
    
    // Show results
    echo "Verificando dados inseridos:\n";
    
    $stmt = $pdo->query("SELECT option_type, COUNT(*) as count FROM inventory_options GROUP BY option_type");
    while ($row = $stmt->fetch()) {
        echo "- {$row['option_type']}: {$row['count']} opções\n";
    }
    
    echo "\n✓ Tabela de opções criada e populada com sucesso!\n";
    
} catch (Exception $e) {
    echo "✗ Erro: " . $e->getMessage() . "\n";
}
?>
