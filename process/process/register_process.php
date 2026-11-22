<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = trim($_POST['email']);
    $contact_number = trim($_POST['contact_number']);
    $role = 'visitor';
    $security_question = trim($_POST['security_question']);
    $security_answer = trim($_POST['security_answer']);

    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Passwords do not match';
        header('Location: ../index.php?page=register');
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $stmt->execute([':username' => $username, ':email' => $email]);

        if ($stmt->fetch()) {
            $_SESSION['error'] = 'Username or email already exists';
            header('Location: ../index.php?page=register');
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $patient_id = null;

        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role, patient_id, security_question, security_answer, status) VALUES (:username, :password, :email, :role, :patient_id, :security_question, :security_answer, 'pending')");
        $stmt->execute([
            ':username' => $username,
            ':password' => $hashed_password,
            ':email' => $email,
            ':role' => $role,
            ':patient_id' => $patient_id,
            ':security_question' => $security_question,
            ':security_answer' => password_hash($security_answer, PASSWORD_DEFAULT)
        ]);

        $_SESSION['success'] = 'Registration successful! Your account is pending admin approval. You will be notified once approved.';
        header('Location: ../index.php?page=login');
        exit();

    } catch(PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        header('Location: ../index.php?page=register');
        exit();
    }
} else {
    header('Location: ../index.php?page=register');
    exit();
}
?>
