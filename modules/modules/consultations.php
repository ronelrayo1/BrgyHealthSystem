<?php
require_once __DIR__ . '/../config/module_init.php';
require_once __DIR__ . '/../classes/Consultation.php';
require_once __DIR__ . '/../classes/Patient.php';

$consultation = new Consultation($pdo);
$patient = new Patient($pdo);
$patients = $patient->getAllPatients();
$consultations = $consultation->getAllConsultations();
?>



    <div class="dashboard-container">
        <?php include __DIR__ . '/../components/sidebar.php'; ?>

        <main class="dashboard-content">
            <h1>Consultations</h1>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert-error">
                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert-success">
                    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <div class="margin-bottom-24">
                <button class="btn-edit btn-padding" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> New Consultation
                </button>
            </div>

            <div class="table-wrapper">
                <table>
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Date</th>
                        <th>Complaint</th>
                        <th>Diagnosis</th>
                        <th>Physician</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($consultations)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No consultations found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($consultations as $c): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($c['first_name'] . ' ' . $c['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($c['consultation_date']); ?></td>
                                <td><?php echo htmlspecialchars($c['chief_complaint']); ?></td>
                                <td><?php echo htmlspecialchars($c['diagnosis'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($c['attending_physician'] ?? '-'); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-delete" onclick="deleteConsultation(<?php echo $c['id']; ?>, this)">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </main>
    </div>

    <div class="modal" id="addModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>New Consultation</h2>
            </div>
            <form class="modal-form" method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-group full-width">
                    <label for="patientId">Patient</label>
                    <select name="patient_id" id="patientId" required>
                        <option value="">Select Patient</option>
                        <?php foreach ($patients as $p): ?>
                            <option value="<?php echo $p['id']; ?>">
                                <?php echo htmlspecialchars($p['first_name'] . ' ' . $p['last_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="consultationDate">Consultation Date</label>
                    <input type="date" name="consultation_date" id="consultationDate" required>
                </div>
                <div class="form-group">
                    <label for="physician">Attending Physician</label>
                    <input type="text" name="attending_physician" id="physician" required>
                </div>
                <div class="form-group full-width">
                    <label for="chiefComplaint">Chief Complaint</label>
                    <textarea name="chief_complaint" id="chiefComplaint" rows="3" required></textarea>
                </div>
                <div class="form-group full-width">
                    <label for="diagnosis">Diagnosis</label>
                    <textarea name="diagnosis" id="diagnosis" rows="3"></textarea>
                </div>
                <div class="form-group full-width">
                    <label for="treatment">Treatment</label>
                    <textarea name="treatment" id="treatment" rows="3"></textarea>
                </div>
                <div class="form-group full-width">
                    <label for="notes">Notes</label>
                    <textarea name="notes" id="notes" rows="3"></textarea>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn-modal btn-cancel" onclick="closeAddModal()">Cancel</button>
                    <button type="submit" class="btn-modal btn-save">Add Consultation</button>
                </div>
            </form>
        </div>
    </div>
