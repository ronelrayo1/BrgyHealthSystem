<?php
require_once __DIR__ . '/../config/session_init.php';

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Session expired. Please login again.']);
    exit();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Patient.php';

$patient = new Patient($pdo);
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$patientId = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($patientId) {
    // Show only the specific patient
    $patientData = $patient->getPatientById($patientId);
    $patients = $patientData ? [$patientData] : [];
} elseif ($search) {
    $patients = $patient->searchPatients($search);
} else {
    $patients = $patient->getAllPatients();
}
?>



<div class="dashboard-container">
    <?php include __DIR__ . '/../components/sidebar.php'; ?>

    <main class="dashboard-content">
        <h1>Patient Profiles</h1>

        <div class="search-box">
            <input type="text" id="searchPatients" placeholder="Search patients by name..." onkeyup="filterPatients()">
        </div>

        <div class="patients-grid" id="patientsGrid">
            <?php if (empty($patients)): ?>
                <p class="center-color grid-full-width padding-40">No patients found</p>
            <?php else: ?>
                <?php foreach ($patients as $p): ?>
                    <div class="patient-card" data-name="<?php echo strtolower(htmlspecialchars($p['first_name'] . ' ' . $p['last_name'])); ?>">
                        <div class="patient-card-header">
                            <div class="patient-avatar">
                                <?php echo strtoupper(substr($p['first_name'], 0, 1)); ?>
                            </div>
                            <div class="patient-info">
                                <h3><?php echo htmlspecialchars($p['first_name'] . ' ' . $p['last_name']); ?></h3>
                                <p>Patient ID: <?php echo str_pad($p['id'], 3, '0', STR_PAD_LEFT); ?></p>
                            </div>
                        </div>
                        <div class="patient-card-details">
                            <p><strong>Contact:</strong> <?php echo htmlspecialchars($p['contact_number']); ?></p>
                            <p><strong>Gender:</strong> <?php echo htmlspecialchars($p['gender']); ?></p>
                        </div>
                        <button class="open-profile-btn" onclick="openProfile(<?php echo $p['id']; ?>)">Open Profile</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
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
