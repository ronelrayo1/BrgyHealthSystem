<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $token = $_POST['token'] ?? '';

    if (empty($token)) {
        $_SESSION['error'] = 'Invalid reset link. Please request a new password reset.';
        header('Location: ../index.php?page=forgot_password');
        exit();
    }

    if (strlen($password) < 6) {
        $_SESSION['error'] = 'Password must be at least 6 characters long';
        header('Location: ../index.php?page=reset_password&token=' . urlencode($token));
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Passwords do not match';
        header('Location: ../index.php?page=reset_password&token=' . urlencode($token));
        exit();
    }

    try {
        $currentTime = time();
        $stmt = $pdo->prepare("SELECT * FROM password_reset_tokens WHERE token = :token AND used_at IS NULL AND expires_at > :current_time");
        $stmt->execute([':token' => $token, ':current_time' => $currentTime]);
        $resetToken = $stmt->fetch();

        if (!$resetToken) {
            $_SESSION['error'] = 'Invalid or expired reset link. Please request a new password reset.';
            header('Location: ../index.php?page=forgot_password');
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->execute([
            ':password' => $hashed_password,
            ':id' => $resetToken['user_id']
        ]);

        $stmt = $pdo->prepare("UPDATE password_reset_tokens SET used_at = NOW() WHERE token = :token");
        $stmt->execute([':token' => $token]);

        $_SESSION['success'] = 'Password has been reset successfully. Please login with your new password.';
        header('Location: ../index.php?page=login');
        exit();

    } catch(PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        header('Location: ../index.php?page=reset_password&token=' . urlencode($token));
        exit();
    }
} else {
    header('Location: ../index.php?page=login');
    exit();
}
?>
