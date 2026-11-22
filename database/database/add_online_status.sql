-- Add online status to users table
-- Run this in phpMyAdmin or MySQL

USE healthsystem;

-- Add is_online column if it doesn't exist
SET @dbname = DATABASE();
SET @tablename = 'users';
SET @columnname = 'is_online';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_schema = @dbname)
      AND (table_name = @tablename)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' TINYINT(1) NOT NULL DEFAULT 0 AFTER status')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add last_activity column if it doesn't exist
SET @columnname = 'last_activity';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_schema = @dbname)
      AND (table_name = @tablename)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' TIMESTAMP NULL DEFAULT NULL AFTER is_online')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Existing users are offline by default
