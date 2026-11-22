<?php
if (!isset($_GET['page']) || basename($_SERVER['REQUEST_URI']) == 'register.php') {
    header('Location: ../index.php?page=register');
    exit();
}

require_once __DIR__ . '/../config/session_init.php';
require_once __DIR__ . '/../config/database.php';

$scriptPath = $_SERVER['SCRIPT_NAME'];
if (strpos($scriptPath, '/pages/') !== false) {
    $basePath = '../';
} else {
    $basePath = '';
}
?>
  <?php include __DIR__ . '/../components/alerts.php'; ?>

<form action="<?php echo $basePath; ?>process/register_process.php" method="post" class="auth-form">
    <div class="form-logo">
        <img src="<?php echo $basePath; ?>assets/logo/icon.png" alt="logo" width="120px">
    </div>
    <h2>Create Account</h2>
    <p class="form-subtitle">Register to access the health center system</p>

    <input type="hidden" name="role" value="visitor">

    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required placeholder="Enter username" autocomplete="username">
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required placeholder="Enter email" autocomplete="email">
    </div>

    <div class="form-group">
        <label for="contact_number">Contact Number</label>
        <input type="tel" id="contact_number" name="contact_number" required placeholder="Enter contact number" autocomplete="tel">
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required placeholder="Enter password" autocomplete="new-password">
    </div>

    <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm password" autocomplete="new-password">
    </div>

    <div class="form-group">
        <label for="security_question">Security Question</label>
        <select id="security_question" name="security_question" required>
            <option value="">Select a security question</option>
            <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
            <option value="What was the name of your first pet?">What was the name of your first pet?</option>
            <option value="What elementary school did you attend?">What elementary school did you attend?</option>
            <option value="What is your favorite color?">What is your favorite color?</option>
            <option value="In what city were you born?">In what city were you born?</option>
            <option value="What is the name of your favorite teacher?">What is the name of your favorite teacher?</option>
        </select>
    </div>

    <div class="form-group">
        <label for="security_answer">Security Answer</label>
        <input type="text" id="security_answer" name="security_answer" required placeholder="Enter your answer" autocomplete="off">
    </div>

    <input type="submit" value="Register" class="submit-btn">

    <div class="form-footer">
        <p>Already have an account? <a href="<?php echo $basePath; ?>index.php?page=login">Login here</a></p>
    </div>
</form>