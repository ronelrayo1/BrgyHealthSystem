<?php
require_once '../config/session_init.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {

            $status = $user['status'] ?? 'approved';
            if ($status === 'pending') {
                $_SESSION['error'] = 'Your account is pending admin approval. Please wait for approval before logging in.';
                header('Location: ../index.php?page=login');
                exit();
            }
            if ($status === 'rejected') {
                $_SESSION['error'] = 'Your account registration was rejected. Please contact the administrator.';
                header('Location: ../index.php?page=login');
                exit();
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if (isset($user['patient_id'])) {
                $_SESSION['patient_id'] = $user['patient_id'];
            }

            if ($user['role'] === 'visitor') {
                $checkStmt = $pdo->prepare("SELECT id FROM visitor_info WHERE user_id = :user_id");
                $checkStmt->execute([':user_id' => $user['id']]);
                if ($checkStmt->fetch()) {
                    $_SESSION['visitor_info_completed'] = true;
                }
            }

            $stmt = $pdo->prepare("UPDATE users SET is_online = 1, last_activity = NOW() WHERE id = :id");
            $stmt->execute([':id' => $user['id']]);

            header('Location: ../index.php?page=dashboard');
            exit();
        } else {
            $_SESSION['error'] = 'Invalid username or password';
            header('Location: ../index.php?page=login');
            exit();
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        header('Location: ../index.php?page=login');
        exit();
    }
} else {
    header('Location: ../index.php?page=login');
    exit();
}
?>
