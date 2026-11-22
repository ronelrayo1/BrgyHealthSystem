<?php
session_start();
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['username'])) {
    $username = trim($_GET['username']);

    try {
        $stmt = $pdo->prepare("SELECT security_question FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user && !empty($user['security_question'])) {
            echo json_encode(['success' => true, 'security_question' => $user['security_question']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Security question not found']);
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
