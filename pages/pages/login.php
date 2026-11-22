<?php
require_once __DIR__ . '/../config/oauth_config.php';

$google_configured = (GOOGLE_CLIENT_ID !== 'YOUR_GOOGLE_CLIENT_ID_HERE');
$facebook_configured = (FACEBOOK_APP_ID !== 'YOUR_FACEBOOK_APP_ID_HERE');

$scriptPath = $_SERVER['SCRIPT_NAME'];
if (strpos($scriptPath, '/pages/') !== false) {
    $basePath = '../';
} else {
    $basePath = '';
}
?>
<form action="<?php echo $basePath; ?>process/login_process.php" method="post" class="auth-form">
    <div class="form-logo">
        <img src="<?php echo $basePath; ?>assets/logo/icon.png" alt="logo" width="120px">
    </div>
    <h2>Welcome Back</h2>
    <p class="form-subtitle">Login to access the health center system</p>

    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required placeholder="Enter username" autocomplete="username">
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required placeholder="Enter password" autocomplete="current-password">
    </div>

    <div class="form-links">
        <a href="<?php echo $basePath; ?>index.php?page=forgot_password">Forgot Password?</a>
    </div>

    <input type="submit" value="Login" class="submit-btn">

    <div class="social-login">
        <div class="social-divider">
            <span>or continue with</span>
        </div>
        <?php if ($facebook_configured): ?>
            <a href="https://www.facebook.com/v18.0/dialog/oauth?client_id=<?php echo FACEBOOK_APP_ID; ?>&redirect_uri=<?php echo urlencode(FACEBOOK_REDIRECT_URI); ?>&scope=email" class="social-btn social-btn-facebook">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                Continue with Facebook
            </a>
        <?php else: ?>
            <button type="button" class="social-btn social-btn-facebook" disabled title="Facebook OAuth not configured">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                Facebook (Not Configured)
            </button>
        <?php endif; ?>

        <?php if ($google_configured): ?>
            <a href="https://accounts.google.com/o/oauth2/v2/auth?client_id=<?php echo GOOGLE_CLIENT_ID; ?>&redirect_uri=<?php echo urlencode(GOOGLE_REDIRECT_URI); ?>&response_type=code&scope=email%20profile" class="social-btn social-btn-google">
                <span class="google-icon"></span>
                Continue with Google
            </a>
        <?php else: ?>
            <button type="button" class="social-btn social-btn-google" disabled title="Google OAuth not configured">
                <span class="google-icon"></span>
                Google (Not Configured)
            </button>
        <?php endif; ?>
    </div>

    <div class="form-footer">
        <p>Don't have an account? <a href="<?php echo $basePath; ?>index.php?page=register">Register here</a></p>
    </div>
</form>
