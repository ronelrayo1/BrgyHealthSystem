# Setup Folder

This folder contains all database setup and configuration scripts for the Barangay Health Center Records System.

## Files

### database_setup.sql
SQL file containing the complete database schema and sample data. Includes:
- All table definitions (patients, users, medical_records, consultations, medicine_inventory, stock_movements)
- Sample patient data
- Default user accounts (admin, staff, doctor)

### setup_database.php
**Run this first** to set up the entire database automatically.
- Creates the database and all tables
- Inserts sample data
- Creates default user accounts

**Access:** http://localhost/healthsystem/setup/setup_database.php

### test_database.php
Test script to verify database connection and check table status.
- Shows if database connection is working
- Lists all tables and record counts
- Tests query execution

**Access:** http://localhost/healthsystem/setup/test_database.php

### setup_admin.php
Creates or updates the admin account.
- If admin exists: updates password
- If admin doesn't exist: creates new admin account

**Default credentials:**
- Username: admin
- Password: admin123

**Access:** http://localhost/healthsystem/setup/setup_admin.php

### setup_visitor.php
Creates or updates the visitor account.
- If visitor exists: updates password
- If visitor doesn't exist: creates new visitor account

**Default credentials:**
- Username: visitor
- Password: visitor123

**Access:** http://localhost/healthsystem/setup/setup_visitor.php

### update_database.php
Updates the database schema to include new user roles.
- Adds 'doctor', 'visitor', and 'guardian' to the role enum

**Access:** http://localhost/healthsystem/setup/update_database.php

### create_admin.php
Utility script to generate password hashes.
- Generates hashes for admin123, staff123, and doctor123
- Useful for updating the SQL file with correct hashes

**Access:** http://localhost/healthsystem/setup/create_admin.php

## Setup Instructions

1. **First-time setup:**
   - Run `setup_database.php` to create the database and all tables
   - The default admin credentials are: username `admin`, password `admin123`

2. **Reset admin password:**
   - Run `setup_admin.php` to reset or create the admin account

3. **Test database:**
   - Run `test_database.php` to verify everything is working

4. **Delete setup files:**
   - After setup is complete, delete this folder for security

## Default User Accounts

After running setup_database.php, these accounts will be created:

- **Admin:** username `admin`, password `admin123`
- **Staff:** username `staff`, password `staff123`
- **Doctor:** username `doctor`, password `doctor123`
