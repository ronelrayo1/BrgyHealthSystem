<?php
require_once __DIR__ . '/../config/module_init.php';

header('Content-Type: application/json');

$userRole = $_SESSION['role'] ?? 'staff';

if ($_POST['action'] == 'delete_patient') {
    if ($userRole !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Access denied. Only admins can delete patients.']);
        exit;
    }

    $deletePatientId = intval($_POST['patient_id']);
    try {
        $stmt = $pdo->prepare("DELETE FROM patients WHERE id = :id");
        $stmt->execute([':id' => $deletePatientId]);
        echo json_encode(['success' => true, 'message' => 'Patient deleted successfully']);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error deleting patient: ' . $e->getMessage()]);
    }
    exit;
}

if ($_POST['action'] == 'edit_patient') {
    require_once __DIR__ . '/../classes/Patient.php';
    $editPatientId = intval($_POST['id']);
    $patientObj = new Patient($pdo);
    $data = [
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'middle_name' => $_POST['middle_name'] ?? '',
        'birth_date' => $_POST['birth_date'],
        'gender' => $_POST['gender'],
        'address' => $_POST['address'],
        'contact_number' => $_POST['contact_number'],
        'guardian_name' => $_POST['guardian_name'] ?? ''
    ];
    $result = $patientObj->updatePatient($editPatientId, $data);
    if ($result['success']) {
        echo json_encode(['success' => true, 'message' => 'Patient updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => implode(', ', $result['errors'])]);
    }
    exit;
}

if ($_POST['action'] == 'register_patient') {
    if (!in_array($userRole ?? '', ['visitor', 'patient', 'guardian'], true) || !empty($_SESSION['patient_id'])) {
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }
    require_once __DIR__ . '/../classes/Patient.php';
    $patientObj = new Patient($pdo);

    $uploadDir = __DIR__ . '/../uploads/patients/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $idFront = '';
    $idBack = '';

    if (isset($_FILES['id_front']) && $_FILES['id_front']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['id_front']['name'], PATHINFO_EXTENSION);
        $idFront = 'id_front_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
        move_uploaded_file($_FILES['id_front']['tmp_name'], $uploadDir . $idFront);
    }

    if (isset($_FILES['id_back']) && $_FILES['id_back']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['id_back']['name'], PATHINFO_EXTENSION);
        $idBack = 'id_back_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
        move_uploaded_file($_FILES['id_back']['tmp_name'], $uploadDir . $idBack);
    }

    $data = [
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'middle_name' => $_POST['middle_name'] ?? '',
        'birth_date' => $_POST['birth_date'] ?? '',
        'gender' => $_POST['gender'],
        'address' => $_POST['address'] ?? '',
        'contact_number' => $_POST['contact_number'],
        'id_number' => $_POST['id_number'] ?? '',
        'guardian_name' => $_POST['guardian_name'] ?? '',
        'id_front' => $idFront,
        'id_back' => $idBack
    ];
    $result = $patientObj->addPatient($data);
    if ($result['success']) {
        $newPatientId = $result['id'];
        $stmt = $pdo->prepare("UPDATE users SET patient_id = :pid, role = 'patient' WHERE id = :uid");
        $stmt->execute([':pid' => $newPatientId, ':uid' => $_SESSION['user_id']]);
        $_SESSION['patient_id'] = $newPatientId;
        $_SESSION['role'] = 'patient';
        unset($_SESSION['skip_profile']);
        echo json_encode(['success' => true, 'message' => 'Patient profile created successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => implode(', ', $result['errors'])]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
exit;
?>
