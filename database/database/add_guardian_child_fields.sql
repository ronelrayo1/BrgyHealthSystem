-- Add guardian_name and child_name columns to patients table
USE healthsystem;

ALTER TABLE patients ADD COLUMN guardian_name VARCHAR(100) AFTER contact_number;
ALTER TABLE patients ADD COLUMN child_name VARCHAR(100) AFTER guardian_name;
