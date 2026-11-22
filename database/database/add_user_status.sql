-- Add approval status to users table
-- Run this in phpMyAdmin or MySQL

USE healthsystem;

ALTER TABLE users
ADD COLUMN status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'approved' AFTER role;

-- Existing users remain approved; only new registrations become pending
