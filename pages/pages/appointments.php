<?php
require_once __DIR__ . '/../config/module_init.php';
require_once __DIR__ . '/../classes/Consultation.php';
require_once __DIR__ . '/../classes/Patient.php';

$consultation = new Consultation($pdo);
$patient = new Patient($pdo);

$patientId = $_SESSION['patient_id'] ?? null;
$userRole = $_SESSION['role'] ?? '';

if (!$patientId && !in_array($userRole, ['admin', 'doctor', 'staff'])) {
    header('Location: index.php?page=dashboard');
    exit();
}

if ($patientId) {
    $consultations = $consultation->getConsultationsByPatient($patientId);
} else {
    $consultations = [];
}
?>



    <div class="dashboard-container">
        <?php include __DIR__ . '/../components/sidebar.php'; ?>

        <main class="dashboard-content">
            <h1>My Appointments</h1>

            <?php if (empty($consultations)): ?>
                <div class="no-appointments">
                    <i class="fas fa-calendar-alt"></i>
                    <p>No appointments found</p>
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Chief Complaint</th>
                            <th>Diagnosis</th>
                            <th>Treatment</th>
                            <th>Physician</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($consultations as $c): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($c['consultation_date']); ?></td>
                                <td><?php echo htmlspecialchars($c['chief_complaint']); ?></td>
                                <td><?php echo htmlspecialchars($c['diagnosis'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($c['treatment'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($c['attending_physician'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($c['notes'] ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            <?php endif; ?>
        </main>
    </div>
