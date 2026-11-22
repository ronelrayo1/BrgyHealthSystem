-- Add profile_picture column to patients table
USE healthsystem;

ALTER TABLE patients ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL AFTER contact_number;
