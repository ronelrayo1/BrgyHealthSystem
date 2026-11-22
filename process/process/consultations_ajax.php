<?php
require_once __DIR__ . '/../config/session_init.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Consultation.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Session expired. Please login again.']);
    exit();
}

header('Content-Type: application/json');

$consultation = new Consultation($pdo);

if ($_POST['action'] == 'delete') {
    $id = intval($_POST['id']);
    try {
        $stmt = $pdo->prepare("DELETE FROM consultations WHERE id = :id");
        $stmt->execute([':id' => $id]);
        echo json_encode(['success' => true, 'message' => 'Consultation deleted successfully']);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error deleting consultation: ' . $e->getMessage()]);
    }
    exit;
}

if ($_POST['action'] == 'add') {
    $data = [
        'patient_id' => $_POST['patient_id'],
        'consultation_date' => $_POST['consultation_date'],
        'chief_complaint' => $_POST['chief_complaint'],
        'diagnosis' => $_POST['diagnosis'],
        'treatment' => $_POST['treatment'],
        'notes' => $_POST['notes'],
        'attending_physician' => $_POST['attending_physician']
    ];

    $result = $consultation->addConsultation($data);
    if ($result['success']) {
        echo json_encode(['success' => true, 'message' => 'Consultation added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => implode(', ', $result['errors'])]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
exit;
?>
