<?php
require_once __DIR__ . '/../config/module_init.php';

if ($_SESSION['role'] !== 'visitor') {
    header('Location: index.php?page=dashboard');
    exit();
}
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="profile-container">
    <form id="visitorForm" class="profile-form">
        <div class="profile-header">
            <h1>Visitor Information</h1>
            <p class="subtitle">Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>! Please fill out your information below to complete your registration.</p>
        </div>

        <div class="form-group">
            <label for="firstName">First Name</label>
            <input type="text" name="first_name" id="firstName" required>
        </div>

        <div class="form-group">
            <label for="lastName">Last Name</label>
            <input type="text" name="last_name" id="lastName" required>
        </div>

        <div class="form-group">
            <label for="middleName">Middle Name</label>
            <input type="text" name="middle_name" id="middleName">
        </div>

        <div class="form-group">
            <label for="gender">Gender</label>
            <select name="gender" id="gender" required>
                <option value="">Select</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>

        <div class="form-group">
            <label for="contactNumber">Contact Number</label>
            <input type="tel" name="contact_number" id="contactNumber" required>
        </div>

        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" name="address" id="address" placeholder="Complete address" required>
        </div>

        <div class="form-group">
            <label for="purpose">Purpose of Visit</label>
            <select name="purpose" id="purpose" required>
                <option value="">Select Purpose</option>
                <option value="inquiry">General Inquiry</option>
                <option value="appointment">Book Appointment</option>
                <option value="consultation">Consultation</option>
                <option value="pickup">Medicine Pickup</option>
                <option value="document">Document Request</option>
                <option value="other">Other</option>
            </select>
        </div>

        <div class="form-group full-width">
            <label for="notes">Additional Notes (Optional)</label>
            <textarea name="notes" id="notes" rows="3" placeholder="Any additional information you'd like to share"></textarea>
        </div>

        <div class="form-group full-width">
            <button type="submit" class="submit-btn">Complete Registration</button>
        </div>
    </form>
</div>
