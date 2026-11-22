<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    unset($_SESSION['role']);
    unset($_SESSION['username']);
    unset($_SESSION['patient_id']);
} else {
    if (isset($pdo)) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET is_online = 1, last_activity = NOW() WHERE id = :id");
            $stmt->execute([':id' => $_SESSION['user_id']]);

            $stmt = $pdo->prepare("UPDATE users SET is_online = 0 WHERE last_activity < DATE_SUB(NOW(), INTERVAL 30 MINUTE)");
            $stmt->execute();

            if (($_SESSION['role'] ?? '') === 'visitor' && empty($_SESSION['visitor_info_completed'])) {
                $checkStmt = $pdo->prepare("SELECT id FROM visitor_info WHERE user_id = :user_id");
                $checkStmt->execute([':user_id' => $_SESSION['user_id']]);
                if ($checkStmt->fetch()) {
                    $_SESSION['visitor_info_completed'] = true;
                }
            }
        } catch(PDOException $e) {
        }
    }
}
?>
