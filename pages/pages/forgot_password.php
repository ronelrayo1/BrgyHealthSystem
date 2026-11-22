<?php
if (!isset($_GET['page']) || basename($_SERVER['REQUEST_URI']) == 'forgot_password.php') {
    header('Location: ../index.php?page=forgot_password');
    exit();
}

require_once __DIR__ . '/../config/session_init.php';
require_once __DIR__ . '/../config/cloudflare_config.php';

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

if (isset($_GET['reset'])) {
    session_unset();
    session_destroy();
    session_start();
    header('Location: ../index.php?page=forgot_password');
    exit();
}

$scriptPath = $_SERVER['SCRIPT_NAME'];
if (strpos($scriptPath, '/pages/') !== false) {
    $basePath = '../';
} else {
    $basePath = '';
}
?>
  <?php include __DIR__ . '/../components/alerts.php'; ?>

<form action="<?php echo $basePath; ?>process/forgot_password_process.php" method="post" class="auth-form">
    <div class="form-logo">
        <img src="<?php echo $basePath; ?>assets/logo/icon.png" alt="logo" width="120px">
    </div>
    <h2>Reset Password</h2>
    <p class="form-subtitle">Enter your username to receive a password reset link via email</p>

    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required placeholder="Enter your username" autocomplete="username">
    </div>
    <div class="form-group">
        <div class="cf-turnstile" data-sitekey="<?php echo CLOUDFLARE_TURNSTILE_SITE_KEY; ?>"></div>
    </div>
    <input type="hidden" name="step" value="1">
    <input type="submit" value="Send Reset Link" class="submit-btn">

    <div class="form-footer">
        <p>Remember your password? <a href="<?php echo $basePath; ?>index.php?page=login">Login here</a></p>
    </div>
</form>

<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

