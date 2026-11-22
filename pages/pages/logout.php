<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("UPDATE users SET is_online = 0 WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['user_id']]);
}

session_unset();
session_destroy();
header('Location: index.php?page=home');
exit();
?>