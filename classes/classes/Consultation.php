<?php
require_once 'Person.php';

class Consultation extends Person {

    public function __construct($pdo) {
        parent::__construct($pdo);
    }
    
    public function addConsultation($data) {
        $errors = $this->validateRequired($data, ['patient_id', 'consultation_date', 'chief_complaint']);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        if (!$this->validateDate($data['consultation_date'])) {
            return ['success' => false, 'errors' => ['Invalid consultation date']];
        }

        $sql = "INSERT INTO consultations (patient_id, consultation_date, chief_complaint, diagnosis, treatment, notes, attending_physician)
                VALUES (:patient_id, :consultation_date, :chief_complaint, :diagnosis, :treatment, :notes, :attending_physician)";

        try {
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':patient_id' => $data['patient_id'],
                ':consultation_date' => $data['consultation_date'],
                ':chief_complaint' => $this->sanitize($data['chief_complaint']),
                ':diagnosis' => $this->sanitize($data['diagnosis'] ?? ''),
                ':treatment' => $this->sanitize($data['treatment'] ?? ''),
                ':notes' => $this->sanitize($data['notes'] ?? ''),
                ':attending_physician' => $this->sanitize($data['attending_physician'] ?? '')
            ]);

            if ($result) {
                return ['success' => true, 'id' => $this->pdo->lastInsertId()];
            }
            return ['success' => false, 'errors' => ['Failed to add consultation']];
        } catch(PDOException $e) {
            error_log("Consultation add error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Database error occurred']];
        }
    }
    
    public function getAllConsultations() {
        $sql = "SELECT c.*, p.first_name, p.last_name, p.birth_date
                FROM consultations c
                JOIN patients p ON c.patient_id = p.id
                ORDER BY c.consultation_date DESC";
        $stmt = $this->pdo->query($sql);
        $consultations = $stmt->fetchAll();

        foreach ($consultations as &$consultation) {
            if (!empty($consultation['birth_date'])) {
                $birth = new DateTime($consultation['birth_date']);
                $today = new DateTime();
                $consultation['patient_age'] = $today->diff($birth)->y;
            }
        }

        return $consultations;
    }
    
    public function getConsultationById($id) {
        $sql = "SELECT c.*, p.first_name, p.last_name 
                FROM consultations c 
                JOIN patients p ON c.patient_id = p.id 
                WHERE c.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    public function getConsultationsByPatient($patient_id) {
        $sql = "SELECT * FROM consultations WHERE patient_id = :patient_id ORDER BY consultation_date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':patient_id' => $patient_id]);
        return $stmt->fetchAll();
    }
    
    public function updateConsultation($id, $data) {
        $errors = $this->validateRequired($data, ['patient_id', 'consultation_date', 'chief_complaint']);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        if (!$this->validateDate($data['consultation_date'])) {
            return ['success' => false, 'errors' => ['Invalid consultation date']];
        }

        $sql = "UPDATE consultations SET
                patient_id = :patient_id,
                consultation_date = :consultation_date,
                chief_complaint = :chief_complaint,
                diagnosis = :diagnosis,
                treatment = :treatment,
                notes = :notes,
                attending_physician = :attending_physician
                WHERE id = :id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':patient_id' => $data['patient_id'],
                ':consultation_date' => $data['consultation_date'],
                ':chief_complaint' => $this->sanitize($data['chief_complaint']),
                ':diagnosis' => $this->sanitize($data['diagnosis'] ?? ''),
                ':treatment' => $this->sanitize($data['treatment'] ?? ''),
                ':notes' => $this->sanitize($data['notes'] ?? ''),
                ':attending_physician' => $this->sanitize($data['attending_physician'] ?? ''),
                ':id' => $id
            ]);

            if ($result) {
                return ['success' => true];
            }
            return ['success' => false, 'errors' => ['Failed to update consultation']];
        } catch(PDOException $e) {
            error_log("Consultation update error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Database error occurred']];
        }
    }
    
    public function deleteConsultation($id) {
        $sql = "DELETE FROM consultations WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([':id' => $id]);

            if ($result) {
                return ['success' => true];
            }
            return ['success' => false, 'errors' => ['Failed to delete consultation']];
        } catch(PDOException $e) {
            error_log("Consultation delete error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Database error occurred']];
        }
    }
    
    public function getConsultationCount() {
        $sql = "SELECT COUNT(*) as count FROM consultations";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    public function getRecentConsultations($limit = 5) {
        $sql = "SELECT c.*, p.first_name, p.last_name, p.birth_date, c.patient_id
                FROM consultations c
                JOIN patients p ON c.patient_id = p.id
                ORDER BY c.consultation_date DESC
                LIMIT :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $consultations = $stmt->fetchAll();

        foreach ($consultations as &$consultation) {
            if (!empty($consultation['birth_date'])) {
                $birth = new DateTime($consultation['birth_date']);
                $today = new DateTime();
                $consultation['patient_age'] = $today->diff($birth)->y;
            }
        }

        return $consultations;
    }
}
?>
