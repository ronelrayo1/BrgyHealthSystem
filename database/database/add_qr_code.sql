-- Add QR code column to patients table
-- Run this in phpMyAdmin or MySQL

USE healthsystem;

-- Add qr_code column if it doesn't exist
SET @dbname = DATABASE();
SET @tablename = 'patients';
SET @columnname = 'qr_code';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_schema = @dbname)
      AND (table_name = @tablename)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' VARCHAR(255) NULL AFTER profile_picture')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;
