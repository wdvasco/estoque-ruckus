-- Update Database Schema for Flexible Import
-- This script modifies the estoque table to allow blank fields during import
-- Run this script to enable flexible CSV/Excel import

USE estoque_ruckus;

-- Remove UNIQUE constraints temporarily to allow duplicates during import
ALTER TABLE estoque DROP INDEX apmac;
ALTER TABLE estoque DROP INDEX serial;

-- Modify columns to allow NULL values and remove NOT NULL constraints
ALTER TABLE estoque 
MODIFY COLUMN apmac VARCHAR(17) NULL COMMENT 'MAC Address of the Access Point',
MODIFY COLUMN apname VARCHAR(100) NULL COMMENT 'Access Point Name/Identifier', 
MODIFY COLUMN model VARCHAR(50) NULL COMMENT 'Equipment Model',
MODIFY COLUMN serial VARCHAR(50) NULL COMMENT 'Serial Number',
MODIFY COLUMN location VARCHAR(200) NULL COMMENT 'Physical Location',
MODIFY COLUMN inclusao DATE NULL COMMENT 'Date added to inventory';

-- Add back indexes but not as UNIQUE (allow duplicates for import flexibility)
ALTER TABLE estoque ADD INDEX idx_apmac (apmac);
ALTER TABLE estoque ADD INDEX idx_apname (apname);
ALTER TABLE estoque ADD INDEX idx_serial (serial);

-- Show updated table structure
DESCRIBE estoque;

SELECT 'Database updated for flexible import!' as status;
