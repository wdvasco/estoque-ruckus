-- Database schema for Ruckus Access Points Inventory System
-- Created: 2025-07-20

-- Create database
CREATE DATABASE IF NOT EXISTS estoque_ruckus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE estoque_ruckus;

-- Users table for authentication
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Inventory table for Access Points
CREATE TABLE estoque (
    id INT AUTO_INCREMENT PRIMARY KEY,
    apmac VARCHAR(17) UNIQUE NOT NULL COMMENT 'MAC Address of the Access Point',
    apname VARCHAR(100) NOT NULL COMMENT 'Access Point Name/Identifier',
    model VARCHAR(50) NOT NULL COMMENT 'Equipment Model',
    serial VARCHAR(50) UNIQUE NOT NULL COMMENT 'Serial Number',
    status ENUM('Active', 'Inactive', 'Maintenance', 'Retired') DEFAULT 'Active' COMMENT 'Operational Status',
    location VARCHAR(200) NOT NULL COMMENT 'Physical Location',
    inclusao DATE NOT NULL COMMENT 'Date added to inventory',
    obs TEXT COMMENT 'Additional observations/notes',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes for better performance
    INDEX idx_apmac (apmac),
    INDEX idx_apname (apname),
    INDEX idx_serial (serial),
    INDEX idx_status (status),
    INDEX idx_location (location)
);

-- Insert sample admin user (password: admin123)
INSERT INTO users (name, email, password) VALUES 
('Administrator', 'admin@estoque.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Sample data for testing
INSERT INTO estoque (apmac, apname, model, serial, status, location, inclusao, obs) VALUES
('00:11:22:33:44:55', 'AP-LOBBY-01', 'R750', 'RK7501234567', 'Active', 'Main Lobby - Floor 1', '2024-01-15', 'Primary lobby access point'),
('00:11:22:33:44:56', 'AP-CONF-01', 'R650', 'RK6501234568', 'Active', 'Conference Room A - Floor 2', '2024-01-16', 'Conference room coverage'),
('00:11:22:33:44:57', 'AP-OFFICE-01', 'R550', 'RK5501234569', 'Maintenance', 'Office Area - Floor 3', '2024-01-17', 'Under maintenance - firmware update needed');
