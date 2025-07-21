<?php
/**
 * Database Configuration
 * Ruckus Access Points Inventory System
 */

// Database connection parameters
define('DB_HOST', 'localhost');
define('DB_NAME', 'estoque_ruckus');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Create database connection
 * @return PDO Database connection object
 */
function getConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

/**
 * Test database connection
 * @return boolean True if connection successful
 */
function testConnection() {
    try {
        $pdo = getConnection();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
