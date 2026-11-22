<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cloudflare_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $turnstileResponse = $_POST['cf-turnstile-response'] ?? '';
    $step = isset($_POST['step']) ? $_POST['step'] : '1';

    try {
        if ($step == '1') {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch();

            if (!$user) {
                $_SESSION['error'] = 'Username not found';
                header('Location: ../index.php?page=forgot_password');
                exit();
            }

            if (empty($user['email'])) {
                $_SESSION['error'] = 'No email associated with this account. Please contact administrator to add your email.';
                header('Location: ../index.php?page=forgot_password');
                exit();
            }

            error_log("Sending password reset to: " . $user['email']);

            $token = bin2hex(random_bytes(32));
            $expiresAt = time() + (RESET_TOKEN_EXPIRY * 60);

            $stmt = $pdo->prepare("DELETE FROM password_reset_tokens WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $user['id']]);

            $stmt = $pdo->prepare("INSERT INTO password_reset_tokens (user_id, token, email, expires_at) VALUES (:user_id, :token, :email, :expires_at)");
            $stmt->execute([
                ':user_id' => $user['id'],
                ':token' => $token,
                ':email' => $user['email'],
                ':expires_at' => $expiresAt
            ]);

            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $basePath = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

            if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
                $resetLink = "https://canon-sixties-discern.ngrok-free.dev/healthsystem/index.php?page=reset_password&token=" . $token;
            } else {
                $resetLink = $protocol . "://" . $host . $basePath . "/index.php?page=reset_password&token=" . $token;
            }
            $subject = "Password Reset Request";
            $message = "Hello " . htmlspecialchars($user['username']) . ",\n\n";
            $message .= "You requested a password reset for your account.\n\n";
            $message .= "Click the link below to reset your password:\n";
            $message .= $resetLink . "\n\n";
            $message .= "This link will expire in " . RESET_TOKEN_EXPIRY . " minutes.\n\n";
            $message .= "If you did not request this, please ignore this email.\n\n";
            $message .= "Best regards,\nHealth System Team";

            $headers = "From: " . FROM_NAME . " <" . FROM_EMAIL . ">\r\n";
            $headers .= "Reply-To: " . FROM_EMAIL . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();

            if (mail($user['email'], $subject, $message, $headers, "-f" . FROM_EMAIL)) {
                $_SESSION['success'] = 'Password reset link has been sent to your email. Please check your inbox.';
                header('Location: ../index.php?page=login');
                exit();
            } else {
                $error = error_get_last();
                error_log("Mail failed: " . ($error['message'] ?? 'Unknown error'));
                $_SESSION['error'] = 'Failed to send email. Please check your email configuration or contact administrator.';
                header('Location: ../index.php?page=forgot_password');
                exit();
            }
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        header('Location: ../index.php?page=forgot_password');
        exit();
    }
} else {
    header('Location: ../index.php?page=forgot_password');
    exit();
}
?>
