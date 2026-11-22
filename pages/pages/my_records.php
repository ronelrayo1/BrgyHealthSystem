<?php
require_once __DIR__ . '/../config/module_init.php';
require_once __DIR__ . '/../classes/MedicalRecord.php';
require_once __DIR__ . '/../classes/Patient.php';

$medicalRecord = new MedicalRecord($pdo);
$patient = new Patient($pdo);

$patientId = $_SESSION['patient_id'] ?? null;
$userRole = $_SESSION['role'] ?? '';

if (!$patientId && !in_array($userRole, ['admin', 'doctor', 'staff'])) {
    header('Location: index.php?page=dashboard');
    exit();
}

if ($patientId) {
    $records = $medicalRecord->getMedicalRecordsByPatient($patientId);
} else {
    $records = [];
}
?>



    <div class="dashboard-container">
        <?php include __DIR__ . '/../components/sidebar.php'; ?>

        <main class="dashboard-content">
            <h1>My Medical Records</h1>

            <?php if (empty($records)): ?>
                <div class="no-records">
                    <i class="fas fa-file-medical"></i>
                    <p>No medical records found</p>
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Vital Signs</th>
                            <th>Allergies</th>
                            <th>Chronic Conditions</th>
                            <th>Surgeries</th>
                            <th>Family History</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $r): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($r['record_date']); ?></td>
                                <td><?php echo htmlspecialchars($r['vital_signs'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($r['allergies'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($r['chronic_conditions'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($r['surgeries'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($r['family_history'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($r['notes'] ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            <?php endif; ?>
        </main>
    </div>
