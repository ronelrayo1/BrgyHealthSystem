<?php
require_once __DIR__ . '/../config/module_init.php';
require_once __DIR__ . '/../classes/Patient.php';
require_once __DIR__ . '/../config/qr_code_config.php';

$patient = new Patient($pdo);
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search) {
    $patients = $patient->searchPatients($search);
} else {
    $patients = $patient->getAllPatients();
}
?>


<div class="dashboard-container">
    <?php include __DIR__ . '/../components/sidebar.php'; ?>

    <main class="dashboard-content">
        <div class="patients-container">
    <div class="patients-header">
        <h1>Patients</h1>
        <button class="btn btn-primary" onclick="openModal()">Add Patient</button>
    </div>

    <div class="search-box">
        <input type="text" id="searchInput" placeholder="Search patients..." value="<?php echo htmlspecialchars($search); ?>">
    </div>

    <div class="patients-grid">
        <?php foreach ($patients as $p): ?>
        <div class="patient-profile-card">
            <div class="patient-profile-header">
                <?php if (!empty($p['profile_picture'])): ?>
                    <img src="<?php echo 'uploads/profile_pictures/' . htmlspecialchars($p['profile_picture']); ?>"
                         alt="Profile"
                         class="patient-profile-picture">
                <?php else: ?>
                    <div class="patient-profile-initials">
                        <?php echo strtoupper(substr($p['first_name'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
                <div class="patient-profile-info">
                    <h3><?php echo htmlspecialchars($p['first_name'] . ' ' . $p['last_name']); ?></h3>
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
                <div class="detail-item">
                    <span class="detail-label">Address:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($p['address']); ?></span>
                </div>
                <?php if (!empty($p['guardian_name'])): ?>
                <div class="detail-item">
                    <span class="detail-label">Guardian:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($p['guardian_name']); ?></span>
                </div>
                <?php endif; ?>
            </div>

            <div class="patient-profile-actions">
                <button class="btn-open-profile" onclick="openPatientProfile(<?php echo $p['id']; ?>)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    Open Profile
                </button>
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
                <button class="btn-edit" onclick="editPatient(<?php echo $p['id']; ?>)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    Edit
                </button>
                <button class="btn-delete" onclick="deletePatient(<?php echo $p['id']; ?>)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                    Delete
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
    </main>
</div>

<div class="modal" id="patientModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Add Patient</h2>
        </div>
        <form class="modal-form" id="patientForm" enctype="multipart/form-data">
            <input type="hidden" name="id" id="patientId">
            <div class="form-group">
                <label>First Name *</label>
                <input type="text" name="first_name" required>
            </div>
            <div class="form-group">
                <label>Last Name *</label>
                <input type="text" name="last_name" required>
            </div>
            <div class="form-group">
                <label>Middle Name</label>
                <input type="text" name="middle_name">
            </div>
            <div class="form-group">
                <label>Birth Date *</label>
                <input type="date" name="birth_date" required>
            </div>
            <div class="form-group">
                <label>Gender *</label>
                <select name="gender" required>
                    <option value="">Select</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="form-group">
                <label>Contact Number *</label>
                <input type="text" name="contact_number" required>
            </div>
            <div class="form-group full-width">
                <label>Address *</label>
                <input type="text" name="address" required>
            </div>
            <div class="form-group full-width">
                <label>Guardian Name</label>
                <input type="text" name="guardian_name">
            </div>
            <div class="form-group full-width">
                <label>Profile Picture</label>
                <div class="profile-upload-container">
                    <input type="file" name="profile_picture" id="profilePictureInput" accept="image/*" class="display-none">
                    <div class="profile-upload-preview" id="profilePreview" onclick="document.getElementById('profilePictureInput').click()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                            <circle cx="12" cy="13" r="4"></circle>
                        </svg>
                        <span>Click to upload profile picture</span>
                    </div>
                    <button type="button" class="btn-remove-profile display-none" id="removeProfileBtn" onclick="removeProfilePicture()">Remove</button>
                </div>
            </div>
            <div class="modal-buttons">
                <button type="button" class="btn-modal btn-cancel" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn-modal btn-save">Save</button>
            </div>
        </form>
    </div>
</div>

<div class="modal" id="qrCodeModal">
    <div class="modal-content qr-modal-content">
        <div class="modal-header">
            <h2>Patient QR Code</h2>
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
        </div>
    </div>
</div>
