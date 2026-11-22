<?php
require_once 'Person.php';

class MedicalRecord extends Person {

    public function __construct($pdo) {
        parent::__construct($pdo);
    }
    
    public function addMedicalRecord($data) {
        $errors = $this->validateRequired($data, ['patient_id', 'record_date']);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        if (!$this->validateDate($data['record_date'])) {
            return ['success' => false, 'errors' => ['Invalid record date']];
        }

        $sql = "INSERT INTO medical_records (patient_id, record_date, vital_signs, allergies, chronic_conditions, surgeries, family_history, notes)
                VALUES (:patient_id, :record_date, :vital_signs, :allergies, :chronic_conditions, :surgeries, :family_history, :notes)";

        try {
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':patient_id' => $data['patient_id'],
                ':record_date' => $data['record_date'],
                ':vital_signs' => $this->sanitize($data['vital_signs'] ?? ''),
                ':allergies' => $this->sanitize($data['allergies'] ?? ''),
                ':chronic_conditions' => $this->sanitize($data['chronic_conditions'] ?? ''),
                ':surgeries' => $this->sanitize($data['surgeries'] ?? ''),
                ':family_history' => $this->sanitize($data['family_history'] ?? ''),
                ':notes' => $this->sanitize($data['notes'] ?? '')
            ]);

            if ($result) {
                return ['success' => true, 'id' => $this->pdo->lastInsertId()];
            }
            return ['success' => false, 'errors' => ['Failed to add medical record']];
        } catch(PDOException $e) {
            error_log("MedicalRecord add error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Database error occurred']];
        }
    }
    
    public function getAllMedicalRecords() {
        $sql = "SELECT mr.*, p.first_name, p.last_name 
                FROM medical_records mr 
                JOIN patients p ON mr.patient_id = p.id 
                ORDER BY mr.record_date DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    
    public function getMedicalRecordById($id) {
        $sql = "SELECT mr.*, p.first_name, p.last_name 
                FROM medical_records mr 
                JOIN patients p ON mr.patient_id = p.id 
                WHERE mr.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    public function getMedicalRecordsByPatient($patient_id) {
        $sql = "SELECT * FROM medical_records WHERE patient_id = :patient_id ORDER BY record_date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':patient_id' => $patient_id]);
        return $stmt->fetchAll();
    }
    
    public function updateMedicalRecord($id, $data) {
        $errors = $this->validateRequired($data, ['patient_id', 'record_date']);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        if (!$this->validateDate($data['record_date'])) {
            return ['success' => false, 'errors' => ['Invalid record date']];
        }

        $sql = "UPDATE medical_records SET
                patient_id = :patient_id,
                record_date = :record_date,
                vital_signs = :vital_signs,
                allergies = :allergies,
                chronic_conditions = :chronic_conditions,
                surgeries = :surgeries,
                family_history = :family_history,
                notes = :notes
                WHERE id = :id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':patient_id' => $data['patient_id'],
                ':record_date' => $data['record_date'],
                ':vital_signs' => $this->sanitize($data['vital_signs'] ?? ''),
                ':allergies' => $this->sanitize($data['allergies'] ?? ''),
                ':chronic_conditions' => $this->sanitize($data['chronic_conditions'] ?? ''),
                ':surgeries' => $this->sanitize($data['surgeries'] ?? ''),
                ':family_history' => $this->sanitize($data['family_history'] ?? ''),
                ':notes' => $this->sanitize($data['notes'] ?? ''),
                ':id' => $id
            ]);

            if ($result) {
                return ['success' => true];
            }
            return ['success' => false, 'errors' => ['Failed to update medical record']];
        } catch(PDOException $e) {
            error_log("MedicalRecord update error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Database error occurred']];
        }
    }
    
    public function deleteMedicalRecord($id) {
        $sql = "DELETE FROM medical_records WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([':id' => $id]);

            if ($result) {
                return ['success' => true];
            }
            return ['success' => false, 'errors' => ['Failed to delete medical record']];
        } catch(PDOException $e) {
            error_log("MedicalRecord delete error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Database error occurred']];
        }
    }
    
    public function getMedicalRecordCount() {
        $sql = "SELECT COUNT(*) as count FROM medical_records";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    public function getPatientLatestRecord($patient_id) {
        $sql = "SELECT * FROM medical_records 
                WHERE patient_id = :patient_id 
                ORDER BY record_date DESC 
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':patient_id' => $patient_id]);
        return $stmt->fetch();
    }
}
?>
