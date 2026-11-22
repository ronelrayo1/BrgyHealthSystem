<?php
require_once __DIR__ . '/../config/page_init.php';

if ($_SESSION['role'] === 'visitor' && empty($_SESSION['visitor_info_completed'])) {
    header('Location: index.php?page=visitor_info');
    exit();
}

if (in_array($_SESSION['role'] ?? '', ['patient', 'guardian'], true)
    && empty($_SESSION['patient_id'])
    && empty($_SESSION['skip_profile'])) {
    header('Location: index.php?page=complete_profile');
    exit();
}

require_once __DIR__ . '/../classes/Patient.php';
require_once __DIR__ . '/../classes/Consultation.php';
require_once __DIR__ . '/../classes/MedicalRecord.php';
require_once __DIR__ . '/../classes/Medicine.php';

$patient = new Patient($pdo);
$consultation = new Consultation($pdo);
$medicalRecord = new MedicalRecord($pdo);
$medicine = new Medicine($pdo);

$userRole = $_SESSION['role'] ?? 'staff';
$patientId = $_SESSION['patient_id'] ?? null;
$patientCount = $patient->getPatientCount();
$consultationCount = $consultation->getConsultationCount();
$recordCount = $medicalRecord->getMedicalRecordCount();
$medicineCount = $medicine->getMedicineCount();
$recentConsultations = $consultation->getRecentConsultations(5);

$allPatients = $patient->getAllPatients();
$allConsultations = $consultation->getAllConsultations();

$pendingUsers = [];
if ($userRole === 'admin') {
    $pendingUsers = $pdo->query("SELECT id, username, email, created_at FROM users WHERE status = 'pending' ORDER BY created_at ASC")->fetchAll();
}

$patientData = null;
$myConsultations = [];
$myRecords = [];
$visitorData = null;

if ($patientId && in_array($userRole, ['patient', 'guardian'])) {
    $patientData = $patient->getPatientById($patientId);
    if ($patientData) {
        $birthDate = new DateTime($patientData['birth_date']);
        $today = new DateTime();
        $patientData['age'] = $today->diff($birthDate)->y;

        $myConsultations = $consultation->getConsultationsByPatient($patientId);
        $myRecords = $medicalRecord->getMedicalRecordsByPatient($patientId);
    }
}

if ($userRole === 'visitor' && !empty($_SESSION['visitor_info_completed'])) {
    $stmt = $pdo->prepare("SELECT * FROM visitor_info WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $visitorData = $stmt->fetch();
}
?>



<div class="dashboard-container">
    <!-- Mobile Menu Toggle Button -->
    <button class="mobile-menu-toggle" onclick="toggleMobileMenu()" aria-label="Toggle menu">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <?php include __DIR__ . '/../components/sidebar.php'; ?>

    <main class="dashboard-content">
        <div class="dashboard-header">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>!</h1>
            <?php if ($userRole !== 'visitor'): ?>
            <p class="role-badge"><?php echo ucfirst($userRole); ?></p>
            <?php endif; ?>
        </div>

    <?php if ($userRole === 'admin' && !empty($pendingUsers)): ?>
    <div class="dashboard-section alert-section">
        <h2><i class="fas fa-bell"></i> Pending User Approvals (<?php echo count($pendingUsers); ?>)</h2>
        <div class="pending-users-list">
            <?php foreach ($pendingUsers as $pending): ?>
                <div class="pending-user-item">
                    <div class="pending-user-info">
                        <strong><?php echo htmlspecialchars($pending['username']); ?></strong>
                        <span><?php echo htmlspecialchars($pending['email']); ?></span>
                        <small>Registered: <?php echo date('M d, Y', strtotime($pending['created_at'])); ?></small>
                    </div>
                    <div class="pending-user-actions">
                        <form method="POST" action="modules/users.php" class="display-inline">
                            <input type="hidden" name="action" value="approve">
                            <input type="hidden" name="user_id" value="<?php echo $pending['id']; ?>">
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Approve this user?')">
                                <i class="fas fa-check"></i> Approve
                            </button>
                        </form>
                        <form method="POST" action="modules/users.php" class="display-inline">
                            <input type="hidden" name="action" value="reject">
                            <input type="hidden" name="user_id" value="<?php echo $pending['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Reject this user?')">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($userRole === 'visitor'): ?>
    <?php if ($visitorData): ?>
    <div class="dashboard-section">
        <h2>My Information</h2>
        <div class="patient-info-card">
            <div class="info-header">
                <div class="info-avatar">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <div class="info-title">
                    <h3><?php echo htmlspecialchars($visitorData['first_name'] . ' ' . $visitorData['last_name']); ?></h3>
                    <span class="info-badge">Visitor</span>
                </div>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                    </div>
                    <div class="info-content">
                        <span class="info-label">Contact</span>
                        <span class="info-value"><?php echo htmlspecialchars($visitorData['contact_number']); ?></span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                    </div>
                    <div class="info-content">
                        <span class="info-label">Address</span>
                        <span class="info-value"><?php echo htmlspecialchars($visitorData['address']); ?></span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div class="info-content">
                        <span class="info-label">Purpose of Visit</span>
                        <span class="info-value"><?php echo htmlspecialchars(ucfirst($visitorData['purpose'])); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="info-featured">
            <div class="featured-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
            </div>
            <div class="featured-content">
                <h4>Verified Visitor</h4>
                <p>Your information is registered in our system</p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="dashboard-section">
        <h2>Welcome to Our Health System</h2>
        <div class="visitor-welcome">
            <div class="welcome-card">
                <div class="welcome-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                        <path d="M2 17l10 5 10-5"/>
                        <path d="M2 12l10 5 10-5"/>
                    </svg>
                </div>
                <h3><?php echo $visitorData ? 'Thank You for Visiting!' : 'Complete Your Registration'; ?></h3>
                <p><?php echo $visitorData ? 'Your information has been recorded. Feel free to explore our services.' : 'To access our full range of healthcare services, please complete your visitor information.'; ?></p>
                <?php if (!$visitorData): ?>
                <a href="index.php?page=visitor_info" class="btn btn-primary">
                    Complete Information
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="dashboard-section">
        <div class="section-header-with-icon">
            <div class="section-icon">
                <i class="fas fa-credit-card"></i>
            </div>
            <div class="section-title">
                <h2>Pay Bills via Online</h2>
                <p>Secure and convenient payment options</p>
            </div>
        </div>
        <div class="quick-actions-grid">
            <div class="action-card action-card-gcash">
                <div class="action-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <h4>Pay Bills</h4>
                <p>Pay via GCash</p>
                <button class="action-btn">Pay Now</button>
            </div>
            <div class="action-card action-card-qr">
                <div class="action-icon">
                    <i class="fas fa-qrcode"></i>
                </div>
                <h4>Scan QR</h4>
                <p>Quick scan payment</p>
                <button class="action-btn">Scan</button>
            </div>
            <div class="action-card action-card-history">
                <div class="action-icon">
                    <i class="fas fa-history"></i>
                </div>
                <h4>History</h4>
                <p>View transactions</p>
                <button class="action-btn">View</button>
            </div>
        </div>
    </div>

    <div class="dashboard-section">
        <h2>Our Services</h2>
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <path d="M12 18v-6"/>
                        <path d="M9 15l3 3 3-3"/>
                    </svg>
                </div>
                <h3>Consultations</h3>
                <p>Book and manage your medical consultations with our qualified doctors.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                </div>
                <h3>Medical Records</h3>
                <p>Access your complete medical history and health records securely.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
                <h3>Appointments</h3>
                <p>Schedule and manage your appointments with healthcare providers.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.5 20.5l10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7z"/>
                        <line x1="8.5" y1="8.5" x2="15.5" y2="15.5"/>
                    </svg>
                </div>
                <h3>Medicine Inventory</h3>
                <p>View available medicines and pharmacy services.</p>
            </div>
        </div>
    </div>

    <div class="dashboard-section">
        <div class="section-header-with-icon">
            <div class="section-icon">
                <i class="fas fa-bolt"></i>
            </div>
            <div class="section-title">
                <h2>Quick Actions</h2>
                <p>Fast access to common tasks</p>
            </div>
        </div>
        <div class="quick-actions">
            <?php if (!$visitorData): ?>
            <a href="index.php?page=visitor_info" class="action-card">
                <div class="action-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <span>Complete Info</span>
            </a>
            <?php else: ?>
            <a href="index.php?page=complete_profile" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <span>Register as Patient</span>
            </a>
            <?php endif; ?>
            <a href="index.php?page=services" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <span>Learn More</span>
            </a>
            <a href="index.php?page=contact" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <span>Contact Us</span>
            </a>
        </div>
    </div>
    <?php endif; ?>

    <?php if (in_array($userRole, ['patient', 'guardian']) && $patientData): ?>
    <div class="dashboard-section">
        <h2>My Profile</h2>
        <div class="patient-info">
            <div class="profile-container">
                <div class="profile-picture-wrapper">
                    <?php if (!empty($patientData['profile_picture'])): ?>
                        <img src="<?php echo 'uploads/profile_pictures/' . htmlspecialchars($patientData['profile_picture']); ?>"
                             alt="Profile Picture"
                             class="profile-picture"
                             id="profilePicture">
                    <?php else: ?>
                        <div class="profile-initials" id="profileInitials">
                            <?php echo strtoupper(substr($patientData['first_name'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <label for="profilePictureInput" class="profile-upload-overlay">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                            <circle cx="12" cy="13" r="4"></circle>
                        </svg>
                    </label>
                    <input type="file" id="profilePictureInput" accept="image/*" class="display-none">
                </div>
            </div>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($patientData['first_name'] . ' ' . $patientData['last_name']); ?></p>
            <p><strong>Contact:</strong> <?php echo htmlspecialchars($patientData['contact_number']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($patientData['address']); ?></p>
            <?php if (!empty($patientData['birth_date'])): ?>
            <p><strong>Birth Date:</strong> <?php echo htmlspecialchars($patientData['birth_date']); ?></p>
            <?php endif; ?>
            <?php if (isset($patientData['age'])): ?>
            <p><strong>Age:</strong> <?php echo $patientData['age']; ?> years old</p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (in_array($userRole, ['admin', 'staff', 'doctor'])): ?>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon patients-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div class="stat-number"><?php echo $patientCount; ?></div>
            <div class="stat-label">Total Patients</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon consultations-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <path d="M12 18v-6"/>
                    <path d="M9 15l3 3 3-3"/>
                </svg>
            </div>
            <div class="stat-number"><?php echo $consultationCount; ?></div>
            <div class="stat-label">Consultations</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon records-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
            </div>
            <div class="stat-number"><?php echo $recordCount; ?></div>
            <div class="stat-label">Medical Records</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon medicine-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.5 20.5l10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7z"/>
                    <line x1="8.5" y1="8.5" x2="15.5" y2="15.5"/>
                </svg>
            </div>
            <div class="stat-number"><?php echo $medicineCount; ?></div>
            <div class="stat-label">Medicines</div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (in_array($userRole, ['admin', 'staff', 'doctor'])): ?>
    <div class="dashboard-section">
        <div class="section-header">
            <h2>Patient Profiles</h2>
            <a href="index.php?page=patients" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="8.5" cy="7" r="4"/>
                    <line x1="20" y1="8" x2="20" y2="14"/>
                    <line x1="23" y1="11" x2="17" y2="11"/>
                </svg>
                View All Patients
            </a>
        </div>

        <div class="patients-grid">
            <?php
            $recentPatients = array_slice($allPatients, 0, 6);
            foreach ($recentPatients as $p):
            ?>
            <div class="patient-profile-card">
                <div class="patient-profile-header">
                    <?php if (!empty($p['profile_picture'])): ?>
                        <img src="<?php echo 'uploads/profile_pictures/' . htmlspecialchars($p['profile_picture']); ?>"
                             alt="Profile"
                             class="patient-profile-picture cursor-pointer"
                             onclick="openPatientProfile(<?php echo $p['id']; ?>)">
                    <?php else: ?>
                        <div class="patient-profile-initials cursor-pointer"
                             onclick="openPatientProfile(<?php echo $p['id']; ?>)">
                            <?php echo strtoupper(substr($p['first_name'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <div class="patient-profile-info">
                        <h3 class="cursor-pointer" onclick="openPatientProfile(<?php echo $p['id']; ?>)"><?php echo htmlspecialchars($p['first_name'] . ' ' . $p['last_name']); ?></h3>
                        <div class="patient-badges">
                            <span class="patient-gender"><?php echo htmlspecialchars($p['gender']); ?></span>
                            <?php if (isset($p['age'])): ?>
                            <span class="patient-age"><?php echo $p['age']; ?> years old</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="patient-profile-details">
                    <div class="detail-item">
                        <span class="detail-label">Birth Date:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($p['birth_date']); ?></span>
                    </div>
                    <?php if (isset($p['age'])): ?>
                    <div class="detail-item">
                        <span class="detail-label">Age:</span>
                        <span class="detail-value"><?php echo $p['age']; ?> years old</span>
                    </div>
                    <?php endif; ?>
                    <div class="detail-item">
                        <span class="detail-label">Contact:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($p['contact_number']); ?></span>
                    </div>
                </div>

                <div class="patient-profile-actions">
                    <?php if (!empty($p['qr_code'])): ?>
                        <button class="btn-qr" onclick="viewQRCode(<?php echo $p['id']; ?>, '<?php echo htmlspecialchars($p['qr_code']); ?>')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                            View QR
                        </button>
                    <?php else: ?>
                        <button class="btn-qr btn-qr-generate" onclick="generateQRCode(<?php echo $p['id']; ?>)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            Generate QR
                        </button>
                    <?php endif; ?>
                    <button class="btn-open-profile" onclick="openPatientProfile(<?php echo $p['id']; ?>)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Open Profile
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($userRole === 'staff'): ?>
    <div class="dashboard-section">
        <h2>Patient Management</h2>
        <div class="action-buttons-container">
            <a href="index.php?page=patients" class="btn btn-primary btn-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="8.5" cy="7" r="4"/>
                    <line x1="20" y1="8" x2="20" y2="14"/>
                    <line x1="23" y1="11" x2="17" y2="11"/>
                </svg>
                Create Patient Profile
            </a>
        </div>
    </div>
    <?php endif; ?>

    <?php if (in_array($userRole, ['admin', 'staff'])): ?>
    <div class="dashboard-section">
        <h2>Recent Consultations</h2>
        <?php if (!empty($recentConsultations)): ?>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Patient</th>
                        <th>Age</th>
                        <th>Chief Complaint</th>
                        <th>Diagnosis</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentConsultations as $consultation): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($consultation['consultation_date']); ?></td>
                        <td><?php echo htmlspecialchars(($consultation['first_name'] ?? '') . ' ' . ($consultation['last_name'] ?? '')); ?></td>
                        <td><?php echo isset($consultation['patient_age']) ? $consultation['patient_age'] . ' yrs' : '-'; ?></td>
                        <td><?php echo htmlspecialchars($consultation['chief_complaint']); ?></td>
                        <td><?php echo htmlspecialchars($consultation['diagnosis'] ?? 'Pending'); ?></td>
                        <td>
                            <button class="btn-view-profile" onclick="openPatientProfile(<?php echo $consultation['patient_id']; ?>)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                View Profile
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="no-data">No recent consultations found.</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if (in_array($userRole, ['patient', 'guardian']) && !empty($myConsultations)): ?>
    <div class="dashboard-section">
        <h2>My Consultations</h2>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Chief Complaint</th>
                        <th>Diagnosis</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($myConsultations as $consultation): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($consultation['consultation_date']); ?></td>
                        <td><?php echo htmlspecialchars($consultation['chief_complaint']); ?></td>
                        <td><?php echo htmlspecialchars($consultation['diagnosis'] ?? 'Pending'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <?php if (in_array($userRole, ['admin', 'staff'])): ?>
    <div class="dashboard-section">
        <div class="section-header-with-icon">
            <div class="section-icon">
                <i class="fas fa-bolt"></i>
            </div>
            <div class="section-title">
                <h2>Quick Actions</h2>
                <p>Fast access to common tasks</p>
            </div>
        </div>
        <div class="quick-actions">
            <a href="index.php?page=patients" class="action-card">
                <div class="action-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <line x1="20" y1="8" x2="20" y2="14"/>
                        <line x1="23" y1="11" x2="17" y2="11"/>
                    </svg>
                </div>
                <span>Add Patient</span>
            </a>
            <a href="index.php?page=consultations" class="action-card">
                <div class="action-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                </div>
                <span>New Consultation</span>
            </a>
            <a href="index.php?page=medical_records" class="action-card">
                <div class="action-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                </div>
                <span>Add Record</span>
            </a>
            <?php if ($userRole === 'admin'): ?>
            <a href="index.php?page=users" class="action-card">
                <div class="action-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <span>Manage Users</span>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    </main>
</div>

<!-- Patient Profile Modal for Admin/Staff -->
<?php if (in_array($userRole, ['admin', 'staff', 'doctor'])): ?>
<div class="modal" id="patientProfileModal">
    <div class="modal-content profile-modal-content">
        <div class="modal-header">
            <h2>Patient Profile</h2>
            <button class="btn-close-modal" onclick="closePatientProfileModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="patient-profile-modal-body" id="patientProfileContent">
            <p class="center-color">Loading patient profile...</p>
        </div>
    </div>
</div>

<div class="modal" id="qrCodeModal">
    <div class="modal-content qr-modal-content">
        <div class="modal-header">
            <h2>Patient QR Code</h2>
            <button class="btn-close-modal" onclick="closeQRModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="qr-code-display">
            <img id="qrCodeImage" src="" alt="QR Code">
            <p id="qrCodePatientName"></p>
        </div>
        <div class="modal-buttons">
            <button type="button" class="btn-modal btn-cancel" onclick="closeQRModal()">Close</button>
            <button type="button" class="btn-modal btn-save" onclick="downloadQRCode()">Download</button>
        </div>
    </div>
</div>
<?php endif; ?>
