<?php
require_once __DIR__ . '/../config/session_init.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Patient.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Session expired. Please login again.']);
    exit();
}

header('Content-Type: application/json');

$patient = new Patient($pdo);

if ($_POST['action'] == 'get_patient') {
    $id = intval($_POST['id']);
    $patientData = $patient->getPatientById($id);
    if ($patientData) {
        echo json_encode(['success' => true, 'patient' => $patientData]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Patient not found']);
    }
    exit;
}

if ($_POST['action'] == 'delete') {
    $id = intval($_POST['id']);
    try {
        $stmt = $pdo->prepare("DELETE FROM patients WHERE id = :id");
        $stmt->execute([':id' => $id]);
        echo json_encode(['success' => true, 'message' => 'Patient deleted successfully']);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error deleting patient: ' . $e->getMessage()]);
    }
    exit;
}

if ($_POST['action'] == 'add') {
    $data = [
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'middle_name' => $_POST['middle_name'],
        'birth_date' => $_POST['birth_date'],
        'gender' => $_POST['gender'],
        'address' => $_POST['address'],
        'contact_number' => $_POST['contact_number'],
        'guardian_name' => $_POST['guardian_name'] ?? ''
    ];

    $result = $patient->addPatient($data);
    if ($result['success']) {
        $patientId = $result['id'];

        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_picture'];
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $maxSize = 5 * 1024 * 1024;

            if (in_array($file['type'], $allowedTypes) && $file['size'] <= $maxSize) {
                $uploadDir = __DIR__ . '/../uploads/profile_pictures/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'patient_' . $patientId . '_' . time() . '.' . $extension;
                $filepath = $uploadDir . $filename;

                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    $patient->updateProfilePicture($patientId, $filename);
                }
            }
        }

        echo json_encode(['success' => true, 'message' => 'Patient added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => implode(', ', $result['errors'])]);
    }
    exit;
}

if ($_POST['action'] == 'edit') {
    $id = intval($_POST['id']);
    $data = [
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'middle_name' => $_POST['middle_name'],
        'birth_date' => $_POST['birth_date'],
        'gender' => $_POST['gender'],
        'address' => $_POST['address'],
        'contact_number' => $_POST['contact_number'],
        'guardian_name' => $_POST['guardian_name'] ?? ''
    ];

    $result = $patient->updatePatient($id, $data);
    if ($result['success']) {
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_picture'];
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $maxSize = 5 * 1024 * 1024;

            if (in_array($file['type'], $allowedTypes) && $file['size'] <= $maxSize) {
                $uploadDir = __DIR__ . '/../uploads/profile_pictures/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'patient_' . $id . '_' . time() . '.' . $extension;
                $filepath = $uploadDir . $filename;

                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    $patient->updateProfilePicture($id, $filename);
                }
            }
        }

        echo json_encode(['success' => true, 'message' => 'Patient updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => implode(', ', $result['errors'])]);
    }
    exit;
}

if ($_POST['action'] == 'remove_profile') {
    $id = intval($_POST['id']);
    $result = $patient->updateProfilePicture($id, '');
    if ($result['success']) {
        echo json_encode(['success' => true, 'message' => 'Profile picture removed successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove profile picture']);
    }
    exit;
}

if ($_POST['action'] == 'generate_qr') {
    $id = intval($_POST['id']);
    $result = $patient->regenerateQRCode($id);
    if ($result['success']) {
        echo json_encode(['success' => true, 'message' => 'QR code generated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => implode(', ', $result['errors'])]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
exit;
?>
