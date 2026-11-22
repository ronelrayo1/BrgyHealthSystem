<?php
require_once __DIR__ . '/session_init.php';
require_once __DIR__ . '/database.php';

if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare("UPDATE users SET is_online = 1, last_activity = NOW() WHERE id = :id");
        $stmt->execute([':id' => $_SESSION['user_id']]);

        $stmt = $pdo->prepare("UPDATE users SET is_online = 0 WHERE last_activity < DATE_SUB(NOW(), INTERVAL 30 MINUTE)");
        $stmt->execute();
    } catch(PDOException $e) {
    }
}
?>
