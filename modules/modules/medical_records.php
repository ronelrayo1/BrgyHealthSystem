<?php
require_once __DIR__ . '/../config/module_init.php';
require_once __DIR__ . '/../classes/MedicalRecord.php';
require_once __DIR__ . '/../classes/Patient.php';

$medicalRecord = new MedicalRecord($pdo);
$patient = new Patient($pdo);
$patients = $patient->getAllPatients();
$records = $medicalRecord->getAllMedicalRecords();
?>



    <div class="dashboard-container">
        <?php include __DIR__ . '/../components/sidebar.php'; ?>

        <main class="dashboard-content">
            <h1>Medical Records</h1>

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
                    <i class="fas fa-plus"></i> Add Medical Record
                </button>
            </div>

            <div class="table-wrapper">
                <table>
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Date</th>
                        <th>Vital Signs</th>
                        <th>Allergies</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($records)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No records found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($records as $r): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($r['first_name'] . ' ' . $r['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($r['record_date']); ?></td>
                                <td><?php echo htmlspecialchars(substr($r['vital_signs'], 0, 50) . '...'); ?></td>
                                <td><?php echo htmlspecialchars(substr($r['allergies'], 0, 30) . '...'); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-delete" onclick="deleteRecord(<?php echo $r['id']; ?>, this)">
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
                <h2>Add Medical Record</h2>
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
                    <label for="recordDate">Record Date</label>
                    <input type="date" name="record_date" id="recordDate" required>
                </div>
                <div class="form-group full-width">
                    <label for="vitalSigns">Vital Signs</label>
                    <textarea name="vital_signs" id="vitalSigns" rows="3" placeholder="BP, Temperature, Pulse, Respiratory Rate"></textarea>
                </div>
                <div class="form-group full-width">
                    <label for="allergies">Allergies</label>
                    <textarea name="allergies" id="allergies" rows="2" placeholder="Enter known allergies"></textarea>
                </div>
                <div class="form-group full-width">
                    <label for="chronicConditions">Chronic Conditions</label>
                    <textarea name="chronic_conditions" id="chronicConditions" rows="2" placeholder="Enter chronic conditions"></textarea>
                </div>
                <div class="form-group full-width">
                    <label for="surgeries">Previous Surgeries</label>
                    <textarea name="surgeries" id="surgeries" rows="2" placeholder="Enter previous surgeries"></textarea>
                </div>
                <div class="form-group full-width">
                    <label for="familyHistory">Family History</label>
                    <textarea name="family_history" id="familyHistory" rows="2" placeholder="Enter family medical history"></textarea>
                </div>
                <div class="form-group full-width">
                    <label for="notes">Notes</label>
                    <textarea name="notes" id="notes" rows="3" placeholder="Additional notes"></textarea>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn-modal btn-cancel" onclick="closeAddModal()">Cancel</button>
                    <button type="submit" class="btn-modal btn-save">Add Record</button>
                </div>
            </form>
        </div>
    </div>
