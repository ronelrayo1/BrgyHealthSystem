<?php
if (!isset($_GET['page']) || basename($_SERVER['REQUEST_URI']) == 'reset_password.php') {
    header('Location: ../index.php?page=reset_password&token=' . ($_GET['token'] ?? ''));
    exit();
}

require_once __DIR__ . '/../config/session_init.php';
require_once __DIR__ . '/../config/database.php';

$token = $_GET['token'] ?? '';

if (empty($token)) {
    $_SESSION['error'] = 'Invalid reset link. Please request a new password reset.';
    header('Location: index.php?page=forgot_password');
    exit();
}

error_log("Checking token: " . substr($token, 0, 10) . "...");
$currentTime = time();
$stmt = $pdo->prepare("SELECT * FROM password_reset_tokens WHERE token = :token AND used_at IS NULL AND expires_at > :current_time");
$stmt->execute([':token' => $token, ':current_time' => $currentTime]);
$resetToken = $stmt->fetch();

if (!$resetToken) {
    $debugStmt = $pdo->prepare("SELECT * FROM password_reset_tokens WHERE token = :token");
    $debugStmt->execute([':token' => $token]);
    $debugToken = $debugStmt->fetch();

    error_log("Token check failed. Debug token: " . print_r($debugToken, true));
    error_log("Current time: " . $currentTime);

    if ($debugToken) {
        if ($debugToken['used_at']) {
            $_SESSION['error'] = 'This reset link has already been used. Please request a new password reset.';
        } else {
            $_SESSION['error'] = 'This reset link has expired. Please request a new password reset.';
        }
    } else {
        $_SESSION['error'] = 'Invalid reset link. Please request a new password reset.';
    }
    header('Location: index.php?page=forgot_password');
    exit();
}

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

$scriptPath = $_SERVER['SCRIPT_NAME'];
if (strpos($scriptPath, '/pages/') !== false) {
    $basePath = '../';
} else {
    $basePath = '';
}
?>
<?php include __DIR__ . '/../components/alerts.php'; ?>

<form action="<?php echo $basePath; ?>process/reset_password_process.php" method="post" class="auth-form">
    <div class="form-logo">
        <img src="<?php echo $basePath; ?>assets/logo/icon.png" alt="logo" width="120px">
    </div>
    <h2>Reset Password</h2>
    <p class="form-subtitle">Enter your new password</p>

    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <div class="form-group">
        <label for="password">New Password</label>
        <input type="password" id="password" name="password" required placeholder="Enter new password" minlength="6" autocomplete="new-password">
    </div>

    <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm new password" minlength="6" autocomplete="new-password">
    </div>

    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

    <input type="submit" value="Reset Password" class="submit-btn">

    <div class="form-footer">
        <p>Remember your password? <a href="<?php echo $basePath; ?>index.php?page=login">Login here</a></p>
    </div>
</form>
