<?php
session_start();
require_once '../config/database.php';

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("UPDATE users SET is_online = 0 WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['user_id']]);
}

$_SESSION = array();

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

session_destroy();

header('Location: ../index.php?page=login');
exit();
?>
