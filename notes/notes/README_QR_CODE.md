# QR Code Feature Implementation

## What was added:

1. **Database Update**
   - Added `qr_code` column to `patients` table
   - SQL script: `database/add_qr_code.sql`
   - PHP setup script: `setup/add_qr_code.php`

2. **QR Code Generation**
   - Created `config/qr_code_config.php` with QR code generation functions
   - Uses QR Server API (https://api.qrserver.com) to generate QR codes
   - QR codes are saved to `uploads/qr_codes/` directory

3. **Patient Class Updates**
   - Updated `classes/Patient.php` to auto-generate QR codes when creating patients
   - Added `regenerateQRCode()` method for generating QR codes for existing patients

4. **Patients Module Updates**
   - Updated `modules/patients.php` to display:
     - Profile pictures in the table
     - QR code view button for patients with QR codes
     - Generate QR button for patients without QR codes
   - Added QR code modal for viewing and downloading QR codes

5. **AJAX Handler Updates**
   - Updated `process/patients_ajax.php` to handle QR code generation requests

6. **CSS Styles**
   - Added styles for QR code modal, table profile pictures, and QR buttons

## Installation Steps:

### Step 1: Run Database Setup
Open your browser and navigate to:
```
http://localhost/healthsystem/setup/add_qr_code.php
```
This will add the `qr_code` column to your patients table.

### Step 2: Create Uploads Directory
Ensure the following directories exist:
- `uploads/qr_codes/` (for QR code images)
- `uploads/profile_pictures/` (for profile pictures)

The system will automatically create these directories if they don't exist.

### Step 3: Test the Feature
1. Log in as staff or admin
2. Go to Patients page
3. Create a new patient - a QR code will be automatically generated
4. For existing patients, click "Generate" button to create QR codes
5. Click "View QR" button to see and download the QR code

## Features:

- **Automatic QR Generation**: New patients get QR codes automatically
- **Manual QR Generation**: Staff can generate QR codes for existing patients
- **QR Code Viewing**: Staff can view QR codes in a modal
- **QR Code Download**: Staff can download QR code images
- **Profile Pictures**: Patients table now shows profile pictures or initials

## QR Code Data:
Each QR code contains JSON data with:
- Patient ID
- Patient Name
- Birth Date

This allows for easy patient identification when scanning the QR code.
