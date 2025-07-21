<?php
/**
 * Database Setup Script
 * Creates database and tables if they don't exist
 */

try {
    // Connect to MySQL without specifying database
    $pdo = new PDO('mysql:host=localhost', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "=== CONFIGURAÇÃO DO BANCO DE DADOS ===\n\n";
    
    // Create database
    echo "1. Criando banco de dados 'estoque_ruckus'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS estoque_ruckus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Banco criado/verificado\n\n";
    
    // Use the database
    $pdo->exec("USE estoque_ruckus");
    
    // Create users table
    echo "2. Criando tabela 'users'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "✓ Tabela users criada\n";
    
    // Create inventory table
    echo "3. Criando tabela 'inventory'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS inventory (
            id INT AUTO_INCREMENT PRIMARY KEY,
            apmac VARCHAR(17),
            apname VARCHAR(100),
            model VARCHAR(50),
            serial VARCHAR(100),
            status VARCHAR(50) DEFAULT 'Instalado',
            location TEXT,
            inclusao DATE,
            obs TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            INDEX idx_apmac (apmac),
            INDEX idx_model (model),
            INDEX idx_status (status),
            INDEX idx_serial (serial)
        )
    ");
    echo "✓ Tabela inventory criada\n";
    
    // Create options table
    echo "4. Criando tabela 'inventory_options'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS inventory_options (
            id INT AUTO_INCREMENT PRIMARY KEY,
            option_type ENUM('model', 'status') NOT NULL,
            option_value VARCHAR(100) NOT NULL,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            UNIQUE KEY unique_option (option_type, option_value),
            INDEX idx_type (option_type),
            INDEX idx_active (is_active)
        )
    ");
    echo "✓ Tabela inventory_options criada\n\n";
    
    // Insert admin user
    echo "5. Criando usuário admin...\n";
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute(['admin', 'admin@sistema.local', $adminPassword]);
    echo "✓ Usuário admin criado (login: admin, senha: admin123)\n\n";
    
    // Insert default options
    echo "6. Inserindo opções padrão...\n";
    
    // Models
    $models = ['H510', 'MODELOANTIGO', 'P300', 'R510', 'R550', 'R600', 'R650', 'T300', 'T310D', 'T350', 'T350D', 'T510', 'ZF7341', 'ZF7363', 'ZF7372', 'ZF7762'];
    $stmt = $pdo->prepare("INSERT IGNORE INTO inventory_options (option_type, option_value) VALUES ('model', ?)");
    foreach ($models as $model) {
        $stmt->execute([$model]);
    }
    echo "✓ " . count($models) . " modelos inseridos\n";
    
    // Statuses
    $statuses = ['Desaparecido', 'Descarte', 'Estoque', 'Flagged', 'Instalado', 'Queimado', 'Verificar'];
    $stmt = $pdo->prepare("INSERT IGNORE INTO inventory_options (option_type, option_value) VALUES ('status', ?)");
    foreach ($statuses as $status) {
        $stmt->execute([$status]);
    }
    echo "✓ " . count($statuses) . " status inseridos\n\n";
    
    // Show final status
    echo "7. Status final:\n";
    
    $tables = ['users', 'inventory', 'inventory_options'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
        $count = $stmt->fetch()['count'];
        echo "- $table: $count registros\n";
    }
    
    echo "\n✅ BANCO DE DADOS CONFIGURADO COM SUCESSO!\n";
    echo "\nVocê pode agora:\n";
    echo "1. Fazer login com admin/admin123\n";
    echo "2. Importar o arquivo CSV\n";
    echo "3. Usar o sistema normalmente\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>
