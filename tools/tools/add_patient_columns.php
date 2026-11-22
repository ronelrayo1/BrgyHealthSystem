<?php
require_once __DIR__ . '/config/database.php';

try {
    // Check if blood_type column exists
    $checkBloodType = $pdo->query("SHOW COLUMNS FROM patients LIKE 'blood_type'");
    if ($checkBloodType->rowCount() == 0) {
        $sql = "ALTER TABLE patients ADD COLUMN blood_type VARCHAR(10) NULL AFTER contact_number";
        $pdo->exec($sql);
        echo "✓ Successfully added blood_type column to patients table.<br>";
    } else {
        echo "✓ blood_type column already exists in patients table.<br>";
    }

    // Check if emergency_contact column exists
    $checkEmergencyContact = $pdo->query("SHOW COLUMNS FROM patients LIKE 'emergency_contact'");
    if ($checkEmergencyContact->rowCount() == 0) {
        $sql = "ALTER TABLE patients ADD COLUMN emergency_contact VARCHAR(20) NULL AFTER blood_type";
        $pdo->exec($sql);
        echo "✓ Successfully added emergency_contact column to patients table.<br>";
    } else {
        echo "✓ emergency_contact column already exists in patients table.<br>";
    }

    echo "<br><strong>Columns added successfully!</strong>";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
