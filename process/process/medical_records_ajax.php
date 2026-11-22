<?php
require_once __DIR__ . '/../config/session_init.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/MedicalRecord.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Session expired. Please login again.']);
    exit();
}

header('Content-Type: application/json');

$medicalRecord = new MedicalRecord($pdo);

if ($_POST['action'] == 'delete') {
    $id = intval($_POST['id']);
    $result = $medicalRecord->deleteMedicalRecord($id);
    if ($result['success']) {
        echo json_encode(['success' => true, 'message' => 'Medical record deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => implode(', ', $result['errors'])]);
    }
    exit;
}

if ($_POST['action'] == 'add') {
    $data = [
        'patient_id' => $_POST['patient_id'],
        'record_date' => $_POST['record_date'],
        'vital_signs' => $_POST['vital_signs'],
        'allergies' => $_POST['allergies'],
        'chronic_conditions' => $_POST['chronic_conditions'],
        'surgeries' => $_POST['surgeries'],
        'family_history' => $_POST['family_history'],
        'notes' => $_POST['notes']
    ];

    $result = $medicalRecord->addMedicalRecord($data);
    if ($result['success']) {
        echo json_encode(['success' => true, 'message' => 'Medical record added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => implode(', ', $result['errors'])]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
exit;
?>
