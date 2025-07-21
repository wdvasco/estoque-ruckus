-- Create Options Table for Dynamic Form Options
-- This table stores unique models and statuses from imports

USE estoque_ruckus;

-- Create table for storing dynamic options
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
);

-- Insert model options based on CSV analysis (16 unique models found)
INSERT IGNORE INTO inventory_options (option_type, option_value) VALUES
('model', 'H510'),
('model', 'MODELOANTIGO'),
('model', 'P300'),
('model', 'R510'),
('model', 'R550'),
('model', 'R600'),
('model', 'R650'),
('model', 'T300'),
('model', 'T310D'),
('model', 'T350'),
('model', 'T350D'),
('model', 'T510'),
('model', 'ZF7341'),
('model', 'ZF7363'),
('model', 'ZF7372'),
('model', 'ZF7762');

-- Insert status options based on CSV analysis (7 unique statuses found)
INSERT IGNORE INTO inventory_options (option_type, option_value) VALUES
('status', 'Desaparecido'),
('status', 'Descarte'),
('status', 'Estoque'),
('status', 'Flagged'),
('status', 'Instalado'),
('status', 'Queimado'),
('status', 'Verificar');

-- Show created table
DESCRIBE inventory_options;

SELECT 'Options table created successfully!' as status;
