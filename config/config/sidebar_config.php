<?php

$scriptPath = $_SERVER['SCRIPT_NAME'];
if (strpos($scriptPath, '/modules/') !== false) {
    $basePath = '../';
} else {
    $basePath = '';
}
require_once $basePath . 'classes/Pages.php';
require_once $basePath . 'config/database.php';
$pages = new Pages();
$userRole = $_SESSION['role'] ?? 'staff';

$pendingCount = 0;
if ($userRole === 'admin') {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'pending'");
        $pendingCount = $stmt->fetchColumn();
    } catch(PDOException $e) {
        $pendingCount = 0;
    }
}
?>
