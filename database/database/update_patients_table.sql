-- Update patients table to include missing columns
USE healthsystem;

-- Add guardian_name if not exists
ALTER TABLE patients ADD COLUMN IF NOT EXISTS guardian_name VARCHAR(100) AFTER contact_number;

-- Add id_number if not exists
ALTER TABLE patients ADD COLUMN IF NOT EXISTS id_number VARCHAR(50) AFTER guardian_name;

-- Add id_front if not exists
ALTER TABLE patients ADD COLUMN IF NOT EXISTS id_front VARCHAR(255) AFTER id_number;

-- Add id_back if not exists
ALTER TABLE patients ADD COLUMN IF NOT EXISTS id_back VARCHAR(255) AFTER id_front;

-- Ensure address column is TEXT (not VARCHAR)
ALTER TABLE patients MODIFY COLUMN address TEXT NOT NULL;
