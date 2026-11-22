<?php
require_once 'Person.php';
require_once __DIR__ . '/../config/qr_code_config.php';

class Patient extends Person {

    public function __construct($pdo) {
        parent::__construct($pdo);
    }

    private function calculateAge($birthDate) {
        if (empty($birthDate)) {
            return null;
        }

        $birth = new DateTime($birthDate);
        $today = new DateTime();
        $age = $today->diff($birth)->y;
        return $age;
    }
    
    public function addPatient($data) {
        $errors = $this->validateRequired($data, ['first_name', 'last_name', 'birth_date', 'gender', 'address', 'contact_number']);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        if (!empty($data['birth_date']) && !$this->validateDate($data['birth_date'])) {
            return ['success' => false, 'errors' => ['Invalid birth date']];
        }

        $sql = "INSERT INTO patients (first_name, last_name, middle_name, birth_date, gender, address, contact_number, id_number, guardian_name, id_front, id_back)
                VALUES (:first_name, :last_name, :middle_name, :birth_date, :gender, :address, :contact_number, :id_number, :guardian_name, :id_front, :id_back)";

        try {
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':first_name' => $this->sanitize($data['first_name']),
                ':last_name' => $this->sanitize($data['last_name']),
                ':middle_name' => $this->sanitize($data['middle_name'] ?? ''),
                ':birth_date' => $data['birth_date'],
                ':gender' => $this->sanitize($data['gender']),
                ':address' => $this->sanitize($data['address']),
                ':contact_number' => $this->sanitize($data['contact_number']),
                ':id_number' => $this->sanitize($data['id_number'] ?? ''),
                ':guardian_name' => $this->sanitize($data['guardian_name'] ?? ''),
                ':id_front' => $this->sanitize($data['id_front'] ?? ''),
                ':id_back' => $this->sanitize($data['id_back'] ?? '')
            ]);

            if ($result) {
                $patientId = $this->pdo->lastInsertId();

                $qrData = json_encode([
                    'id' => $patientId,
                    'name' => $data['first_name'] . ' ' . $data['last_name'],
                    'birth_date' => $data['birth_date']
                ]);

                $qrFilename = generateQRCode($patientId, $qrData);

                if ($qrFilename) {
                    $updateSql = "UPDATE patients SET qr_code = :qr_code WHERE id = :id";
                    $updateStmt = $this->pdo->prepare($updateSql);
                    $updateStmt->execute([
                        ':qr_code' => $qrFilename,
                        ':id' => $patientId
                    ]);
                }

                return ['success' => true, 'id' => $patientId];
            }
            return ['success' => false, 'errors' => ['Failed to add patient']];
        } catch(PDOException $e) {
            error_log("Patient add error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Database error occurred']];
        }
    }
    
    public function getAllPatients() {
        $sql = "SELECT * FROM patients ORDER BY created_at ASC";
        $stmt = $this->pdo->query($sql);
        $patients = $stmt->fetchAll();

        foreach ($patients as &$patient) {
            $patient['age'] = $this->calculateAge($patient['birth_date']);
        }

        return $patients;
    }
    
    public function getPatientById($id) {
        $sql = "SELECT * FROM patients WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $patient = $stmt->fetch();

        if ($patient) {
            $patient['age'] = $this->calculateAge($patient['birth_date']);
        }

        return $patient;
    }
    
    public function searchPatients($keyword) {
        $sql = "SELECT * FROM patients
                WHERE CONCAT(first_name, ' ', last_name) LIKE :keyword
                OR first_name LIKE :keyword
                OR last_name LIKE :keyword
                OR middle_name LIKE :keyword
                OR contact_number LIKE :keyword
                ORDER BY created_at ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':keyword' => "%$keyword%"]);
        $patients = $stmt->fetchAll();

        foreach ($patients as &$patient) {
            $patient['age'] = $this->calculateAge($patient['birth_date']);
        }

        return $patients;
    }
    
    public function updatePatient($id, $data) {
        $errors = $this->validateRequired($data, ['first_name', 'last_name', 'birth_date', 'gender', 'address', 'contact_number']);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        if (!empty($data['birth_date']) && !$this->validateDate($data['birth_date'])) {
            return ['success' => false, 'errors' => ['Invalid birth date']];
        }

        $sql = "UPDATE patients SET
                first_name = :first_name,
                last_name = :last_name,
                middle_name = :middle_name,
                birth_date = :birth_date,
                gender = :gender,
                address = :address,
                contact_number = :contact_number,
                guardian_name = :guardian_name
                WHERE id = :id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':first_name' => $this->sanitize($data['first_name']),
                ':last_name' => $this->sanitize($data['last_name']),
                ':middle_name' => $this->sanitize($data['middle_name'] ?? ''),
                ':birth_date' => $data['birth_date'],
                ':gender' => $this->sanitize($data['gender']),
                ':address' => $this->sanitize($data['address']),
                ':contact_number' => $this->sanitize($data['contact_number']),
                ':guardian_name' => $this->sanitize($data['guardian_name'] ?? ''),
                ':id' => $id
            ]);

            if ($result) {
                return ['success' => true];
            }
            return ['success' => false, 'errors' => ['Failed to update patient']];
        } catch(PDOException $e) {
            error_log("Patient update error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Database error occurred']];
        }
    }
    
    public function deletePatient($id) {
        $sql = "DELETE FROM patients WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([':id' => $id]);

            if ($result) {
                return ['success' => true];
            }
            return ['success' => false, 'errors' => ['Failed to delete patient']];
        } catch(PDOException $e) {
            error_log("Patient delete error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Database error occurred']];
        }
    }
    
    public function getPatientCount() {
        if (!is_object($this->pdo) || !($this->pdo instanceof PDO)) {
            error_log("PDO is not valid: " . gettype($this->pdo));
            return 0;
        }
        $sql = "SELECT COUNT(*) as count FROM patients";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch();
        return $result['count'];
    }

    public function updateProfilePicture($id, $filename) {
        $sql = "UPDATE patients SET profile_picture = :profile_picture WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':profile_picture' => $this->sanitize($filename),
                ':id' => $id
            ]);

            if ($result) {
                return ['success' => true];
            }
            return ['success' => false, 'errors' => ['Failed to update profile picture']];
        } catch(PDOException $e) {
            error_log("Profile picture update error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Database error occurred']];
        }
    }

    public function regenerateQRCode($id) {
        $patient = $this->getPatientById($id);
        if (!$patient) {
            return ['success' => false, 'errors' => ['Patient not found']];
        }

        $qrData = json_encode([
            'id' => $id,
            'name' => $patient['first_name'] . ' ' . $patient['last_name'],
            'birth_date' => $patient['birth_date']
        ]);

        $qrFilename = generateQRCode($id, $qrData);

        if ($qrFilename) {
            $sql = "UPDATE patients SET qr_code = :qr_code WHERE id = :id";
            try {
                $stmt = $this->pdo->prepare($sql);
                $result = $stmt->execute([
                    ':qr_code' => $qrFilename,
                    ':id' => $id
                ]);

                if ($result) {
                    return ['success' => true, 'qr_code' => $qrFilename];
                }
                return ['success' => false, 'errors' => ['Failed to update QR code']];
            } catch(PDOException $e) {
                error_log("QR code update error: " . $e->getMessage());
                return ['success' => false, 'errors' => ['Database error occurred']];
            }
        }
        return ['success' => false, 'errors' => ['Failed to generate QR code']];
    }
}
?>
