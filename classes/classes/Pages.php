<?php
class Pages {
    private $currentPage;
    
    public function __construct($currentPage = null) {
        $this->currentPage = $currentPage ?? ($_GET['page'] ?? 'home');
    }
    
    public function getCurrentPage() {
        return $this->currentPage;
    }
    
    public function isActive($page) {
        return $this->currentPage === $page;
    }
    
    public function getPageInfo($page) {
        switch($page) {
            case 'home':
                return [
                    'title' => 'Home',
                    'description' => 'Welcome to Barangay Handumanan Health Center',
                    'active' => true
                ];
                
            case 'services':
                return [
                    'title' => 'Services',
                    'description' => 'Our healthcare services',
                    'active' => true
                ];
                
            case 'about':
                return [
                    'title' => 'About',
                    'description' => 'About our health center',
                    'active' => true
                ];
                
            case 'contact':
                return [
                    'title' => 'Contact',
                    'description' => 'Contact us',
                    'active' => true
                ];
                
            case 'login':
                return [
                    'title' => 'Login',
                    'description' => 'Login to your account',
                    'active' => true
                ];
                
            case 'register':
                return [
                    'title' => 'Register',
                    'description' => 'Create a new account',
                    'active' => true
                ];

            case 'complete_profile':
                return [
                    'title' => 'Complete Profile',
                    'description' => 'Complete your patient information',
                    'active' => true
                ];

            case 'visitor_info':
                return [
                    'title' => 'Visitor Information',
                    'description' => 'Complete your visitor information',
                    'active' => true
                ];

            case 'forgot_password':
                return [
                    'title' => 'Forgot Password',
                    'description' => 'Reset your password',
                    'active' => true
                ];

            case 'reset_password':
                return [
                    'title' => 'Reset Password',
                    'description' => 'Create new password',
                    'active' => true
                ];

            case 'logout':
                return [
                    'title' => 'Logout',
                    'description' => 'Logout from your account',
                    'active' => true
                ];

            case 'dashboard':
                return [
                    'title' => 'Dashboard',
                    'description' => 'Your dashboard',
                    'active' => true
                ];
                
            case 'patients':
                return [
                    'title' => 'Patients',
                    'description' => 'Manage patients',
                    'active' => true
                ];
                
            case 'medical_records':
                return [
                    'title' => 'Medical Records',
                    'description' => 'View medical records',
                    'active' => true
                ];
                
            case 'consultations':
                return [
                    'title' => 'Consultations',
                    'description' => 'Manage consultations',
                    'active' => true
                ];
                
            case 'medicine_inventory':
                return [
                    'title' => 'Medicine Inventory',
                    'description' => 'Manage medicine inventory',
                    'active' => true
                ];
                
            case 'users':
                return [
                    'title' => 'User Management',
                    'description' => 'Manage users',
                    'active' => true
                ];

            case 'patient_profiles':
                return [
                    'title' => 'Patient Profiles',
                    'description' => 'View patient profiles with QR codes',
                    'active' => true
                ];

            case 'visit_patient':
                return [
                    'title' => 'Visit Patient',
                    'description' => 'Search and visit patients',
                    'active' => true
                ];

            case 'settings':
                return [
                    'title' => 'Settings',
                    'description' => 'Account settings',
                    'active' => true
                ];
                
            default:
                return [
                    'title' => 'Page',
                    'description' => '',
                    'active' => false
                ];
        }
    }
    
    public function getActiveClass($page) {
        return $this->isActive($page) ? 'active' : '';
    }
    
    public function redirect($page) {
        header("Location: index.php?page=$page");
        exit();
    }
    
    public static function display($page = 'home') {
        switch($page) {
            case 'home':
                require_once __DIR__ . '/../components/landing_section.php';
                break;

            case 'services':
                require_once __DIR__ . '/../pages/services.php';
                break;

            case 'about':
                require_once __DIR__ . '/../pages/about.php';
                break;

            case 'contact':
                require_once __DIR__ . '/../pages/contact.php';
                break;

            case 'login':
                require_once __DIR__ . '/../pages/login.php';
                break;

            case 'register':
                require_once __DIR__ . '/../pages/register.php';
                break;

            case 'complete_profile':
                require_once __DIR__ . '/../pages/complete_profile.php';
                break;

            case 'visitor_info':
                require_once __DIR__ . '/../pages/visitor_info.php';
                break;

            case 'forgot_password':
                require_once __DIR__ . '/../pages/forgot_password.php';
                break;

            case 'reset_password':
                require_once __DIR__ . '/../pages/reset_password.php';
                break;

            case 'logout':
                require_once __DIR__ . '/../pages/logout.php';
                break;

            case 'dashboard':
                require_once __DIR__ . '/../pages/dashboard.php';
                break;

            case 'patients':
                require_once __DIR__ . '/../modules/patients.php';
                break;

            case 'medical_records':
                require_once __DIR__ . '/../modules/medical_records.php';
                break;

            case 'consultations':
                require_once __DIR__ . '/../modules/consultations.php';
                break;

            case 'medicine_inventory':
                require_once __DIR__ . '/../modules/medicine_inventory.php';
                break;

            case 'users':
                require_once __DIR__ . '/../modules/users.php';
                break;

            case 'patient_profiles':
                require_once __DIR__ . '/../modules/patient_profiles.php';
                break;

            case 'visit_patient':
                require_once __DIR__ . '/../pages/visit_patient.php';
                break;

            case 'settings':
                require_once __DIR__ . '/../pages/settings.php';
                break;

            case 'my_records':
                require_once __DIR__ . '/../pages/my_records.php';
                break;

            case 'appointments':
                require_once __DIR__ . '/../pages/appointments.php';
                break;

            default:
                require_once __DIR__ . '/../components/landing_section.php';
                break;
        }
    }
}
?>
