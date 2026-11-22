<?php

$scriptPath = $_SERVER['SCRIPT_NAME'];
if (strpos($scriptPath, '/modules/') !== false || strpos($scriptPath, '/pages/') !== false) {
    $basePath = '../';
} else {
    $basePath = '';
}
$username = $_SESSION['username'] ?? 'User';
$role = $_SESSION['role'] ?? 'staff';
$roleDisplay = ucfirst($role);
?>
