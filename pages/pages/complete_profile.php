<?php
require_once __DIR__ . '/../config/module_init.php';

if (isset($_GET['skip'])) {
    $_SESSION['skip_profile'] = true;
    header('Location: index.php?page=dashboard');
    exit();
}
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


<div class="profile-container">
    <form id="visitorForm" class="profile-form">
        <div class="profile-header">
            <h1>Patient Information</h1>
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
            <label for="birthDate">Birth Date</label>
            <input type="date" name="birth_date" id="birthDate" required>
        </div>

        <div class="form-group">
            <label for="bloodType">Blood Type</label>
            <select name="blood_type" id="bloodType">
                <option value="">Select</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
            </select>
        </div>

        <div class="form-group">
            <label for="emergencyContact">Emergency Contact</label>
            <input type="tel" name="emergency_contact" id="emergencyContact" placeholder="Emergency contact number">
        </div>

        <div class="form-group full-width">
            <button type="submit" class="submit-btn">Complete Registration</button>
        </div>
    </form>
</div>

<script>
document.getElementById('visitorForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    formData.append('action', 'add');

    fetch('process/patients_ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.href = 'index.php?page=dashboard';
        } else {
            alert(data.message || 'Error saving patient information');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error submitting information. Please try again.');
    });
});
</script>
