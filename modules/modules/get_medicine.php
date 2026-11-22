<?php
session_start();
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        $stmt = $pdo->prepare("SELECT * FROM medicine_inventory WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $medicine = $stmt->fetch();

        if ($medicine) {
            echo json_encode(['success' => true, 'medicine' => $medicine]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Medicine not found']);
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
