# Barangay Handumanan Health Center - Project Structure

## Directory Structure

```
new1/
├── classes/              # PHP Classes (Business Logic)
│   ├── Consultation.php
│   ├── MedicalRecord.php
│   ├── Medicine.php
│   ├── Pages.php
│   ├── Patient.php
│   └── Person.php
│
├── components/           # Reusable UI Components
│   ├── alerts.php
│   ├── bg_slider.php
│   ├── footer.php
│   ├── header.php
│   ├── landing_section.php
│   ├── login_form.php
│   ├── navbar.php
│   ├── navbar2.php
│   └── sidebar.php
│
├── config/              # Configuration Files
│   ├── ajax_handlers.php      # AJAX route handlers
│   ├── bootstrap.php          # Application bootstrap (main init)
│   ├── database.php           # Database connection
│   ├── flash_messages.php     # Session flash messages
│   ├── module_init.php        # Module initialization (admin/staff)
│   ├── oauth_config.php      # OAuth configuration
│   ├── page_init.php          # Page initialization (protected pages)
│   └── session_init.php      # Session initialization
│
├── design/              # Design Assets
│   └── style.css             # Main stylesheet
│
├── modules/             # Admin/Staff Modules
│   ├── consultations.php
│   ├── get_medicine.php
│   ├── medical_records.php
│   ├── medicine_inventory.php
│   ├── patient_profiles.php   # Patient profiles with QR codes
│   ├── patients.php
│   └── users.php
│
├── pages/               # Public Pages
│   ├── about.php
│   ├── appointments.php
│   ├── complete_profile.php
│   ├── contact.php
│   ├── dashboard.php
│   ├── forgot_password.php
│   ├── login.php
│   ├── logout.php
│   ├── my_records.php
│   ├── register.php
│   ├── reset_password.php
│   ├── services.php
│   └── settings.php
│
├── process/             # Form Processing Scripts
│   ├── facebook_callback.php
│   ├── forgot_password_process.php
│   ├── get_security_question.php
│   ├── google_callback.php
│   ├── login_process.php
│   ├── logout_process.php
│   ├── register_process.php
│   └── reset_password_process.php
│
├── repositories/        # Repository Pattern (Data Access)
│   ├── BaseModel.php
│   ├── DBRepository.php
│   ├── IPatientRepository.php
│   └── PatientRepository.php
│
├── setup/               # Database Setup Scripts
│   ├── add_columns.php
│   ├── add_stock_movements_table.php
│   ├── create_admin.php
│   ├── generate_password.php
│   ├── setup_admin.php
│   ├── setup_database.php
│   ├── setup_staff.php
│   ├── setup_visitor.php
│   ├── test_database.php
│   └── update_database.php
│
├── index.php            # Main entry point
└── README.md            # This file
```

## Quick Start

### Database Setup
Run the setup scripts in order:
1. `setup/setup_database.php` - Create database tables
2. `setup/setup_admin.php` - Create admin account
3. `setup/setup_staff.php` - Create staff account (optional)
4. `setup/setup_visitor.php` - Create visitor account (optional)

### File Organization

**Classes** (`classes/`)
- Contains all business logic classes
- Follows OOP principles
- Each class handles specific functionality

**Components** (`components/`)
- Reusable UI components
- Can be included in multiple pages
- Keep UI consistent across the application

**Config** (`config/`)
- Configuration files
- Separates configuration from logic
- Easy to modify settings

**Modules** (`modules/`)
- Admin and staff functionality
- Protected pages requiring authentication
- CRUD operations for data management

**Pages** (`pages/`)
- Public-facing pages
- Landing pages, forms, dashboards
- Some require authentication

**Process** (`process/`)
- Form processing scripts
- AJAX endpoints
- Callback handlers for OAuth

**Repositories** (`repositories/`)
- Data access layer
- Repository pattern implementation
- Separates data access from business logic

**Setup** (`setup/`)
- Database setup and migration scripts
- One-time or maintenance scripts
- Not part of normal application flow

## Access Control

- **Admin**: Full access to all modules
- **Staff**: Access to patients, medical records, consultations
- **Doctor**: Access to patients, medical records, consultations
- **Patient**: Access to own records and appointments
- **Guardian**: Access to dependent's records and appointments
- **Visitor**: Public access to registration and login

## Technologies Used

- PHP (Backend)
- MySQL (Database)
- HTML/CSS/JavaScript (Frontend)
- FontAwesome (Icons)
- QRCode.js (QR Code Generation)
- OAuth (Google/Facebook Login)
