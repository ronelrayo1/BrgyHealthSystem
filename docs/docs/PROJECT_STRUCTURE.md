# Project Structure

## Root Directory Structure

```
new1/
├── .devin/                    # Devin configuration
├── .htaccess                  # Apache configuration
├── assets/                    # Static assets
│   ├── js/                    # JavaScript files
│   └── logo/                  # Logo images
├── classes/                   # PHP Classes
│   ├── Consultation.php       # Consultation model
│   ├── MedicalRecord.php      # Medical Record model
│   ├── Medicine.php           # Medicine model
│   ├── Pages.php              # Page routing
│   ├── Patient.php            # Patient model
│   └── Person.php             # Base Person class
├── components/                # Reusable UI components
│   ├── alerts.php             # Flash messages
│   ├── bg_slider.php          # Background slider
│   ├── footer.php             # Footer
│   ├── header.php             # HTML head/meta
│   ├── landing_section.php    # Landing page content
│   ├── login_form.php         # Login modal
│   ├── navbar.php             # Navigation bar
│   ├── navbar2.php            # Alternative navbar
│   └── sidebar.php            # Sidebar menu
├── config/                    # Configuration files
│   ├── ajax_handlers.php      # AJAX routing
│   ├── bootstrap.php          # Main initialization
│   ├── database.php           # Database connection
│   ├── flash_messages.php     # Flash message handling
│   ├── module_init.php        # Module initialization
│   ├── oauth_config.php       # OAuth configuration
│   ├── page_init.php          # Page initialization
│   └── session_init.php       # Session handling
├── database/                  # Database setup files
│   ├── database_setup.sql     # Main database schema
│   ├── add_user_status.sql    # User status column
│   ├── add_guardian_child_fields.sql
│   ├── update_patients_table.sql
│   └── OAUTH_SETUP_GUIDE.md
├── design/                    # Stylesheets
│   └── style.css              # Main stylesheet
├── docs/                      # Documentation
├── json/                      # JSON data files
├── modules/                   # Admin/Staff modules
│   ├── consultations.php      # Consultations management
│   ├── get_medicine.php       # Medicine API
│   ├── medical_records.php     # Medical records management
│   ├── medicine_inventory.php  # Medicine inventory
│   ├── patient_profiles.php    # Patient profiles with QR
│   ├── patients.php            # Patients management
│   └── users.php               # User management
├── notes/                     # Notes and documentation
├── pages/                     # Public/Protected pages
│   ├── about.php              # About page
│   ├── appointments.php        # Appointments
│   ├── complete_profile.php   # Profile completion
│   ├── contact.php            # Contact form
│   ├── dashboard.php          # User dashboard
│   ├── forgot_password.php    # Password recovery
│   ├── login.php              # Login page
│   ├── logout.php             # Logout handler
│   ├── my_records.php         # User's medical records
│   ├── register.php           # Registration
│   ├── reset_password.php     # Password reset
│   ├── services.php           # Services page
│   └── settings.php           # User settings
├── process/                   # Form processing handlers
│   ├── dashboard_ajax.php     # Dashboard AJAX
│   ├── patients_ajax.php      # Patients AJAX
│   ├── facebook_callback.php  # Facebook OAuth
│   ├── forgot_password_process.php
│   ├── get_security_question.php
│   ├── google_callback.php    # Google OAuth
│   ├── login_process.php      # Login handler
│   ├── logout_process.php     # Logout handler
│   ├── register_process.php   # Registration handler
│   └── reset_password_process.php
├── repositories/              # Repository pattern (unused)
│   ├── BaseModel.php
│   ├── DBRepository.php
│   ├── IPatientRepository.php
│   └── PatientRepository.php
├── setup/                     # Setup scripts
│   ├── add_columns.php
│   ├── add_stock_movements_table.php
│   ├── create_admin.php
│   ├── database_setup.sql
│   ├── generate_password.php
│   ├── setup_admin.php
│   ├── setup_database.php
│   ├── setup_staff.php
│   ├── setup_visitor.php
│   ├── test_database.php
│   └── update_database.php
├── tools/                     # Utility scripts
│   └── remove_all_comments.php
├── uploads/                   # User uploads
│   └── patients/              # Patient ID uploads
└── index.php                  # Main entry point
```

## Key Features

### Authentication Flow
1. User registers at `index.php?page=register`
2. Admin approves pending users (if approval system enabled)
3. User logs in at `index.php?page=login`
4. Redirected to dashboard based on role

### Role-Based Access
- **admin**: Full access to all modules and user management
- **staff**: Access to patients, consultations, medical records, medicine inventory
- **doctor**: Access to consultations and medical records
- **patient**: Access to own records and profile
- **visitor**: Must complete profile before accessing features
- **guardian**: Access to dependent's records

### Module Structure
- **pages/**: View-only pages (HTML + CSS + minimal PHP)
- **modules/**: Admin/staff functionality with AJAX
- **process/**: Form submission handlers
- **components/**: Reusable UI components
- **config/**: Configuration and initialization

### Code Organization Principles
1. **Separation of Concerns**: Views separate from business logic
2. **DRY**: Reusable components in `components/`
3. **Centralized Config**: All configuration in `config/`
4. **AJAX Separation**: AJAX handlers in `process/` folder
5. **Class-Based**: Business logic in `classes/`

## File Path Conventions
- Relative paths for same-level references
- `../` for parent directory
- Use `components/header.php` for consistent asset loading
- CSS in `design/style.css`
- JS in `assets/js/`
- Images in `assets/logo/`

## Recent Changes
- Removed duplicate `repository/` folder
- Consolidated `assets/design/` into `design/`
- Updated all pages to use `components/header.php`
- Separated AJAX logic from view files
- Removed single-line comments for cleaner code
