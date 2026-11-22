<?php
require_once __DIR__ . '/../config/session_init.php';

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Session expired. Please login again.']);
    exit();
}

if (!isset($_SESSION['patient_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Patient ID not found in session.']);
    exit();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Patient.php';

$patientId = $_SESSION['patient_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['profile_picture'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit();
}

$file = $_FILES['profile_picture'];

$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
$maxSize = 5 * 1024 * 1024;

if (!in_array($file['type'], $allowedTypes)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.']);
    exit();
}

if ($file['size'] > $maxSize) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'File size exceeds 5MB limit.']);
    exit();
}

if ($file['error'] !== UPLOAD_ERR_OK) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'File upload error.']);
    exit();
}

$uploadDir = __DIR__ . '/../uploads/profile_pictures/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'patient_' . $patientId . '_' . time() . '.' . $extension;
$filepath = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $filepath)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file.']);
    exit();
}

$patient = new Patient($pdo);
$updateResult = $patient->updateProfilePicture($patientId, $filename);

if ($updateResult['success']) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Profile picture updated successfully.',
        'filename' => $filename
    ]);
} else {
    unlink($filepath);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Failed to update database.']);
}
?>
