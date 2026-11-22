<?php
require_once __DIR__ . '/../config/database.php';

try {
    $sql = "ALTER TABLE patients ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL AFTER contact_number";
    $pdo->exec($sql);
    echo "<p style='color: green; font-weight: bold;'>✓ Profile picture column added successfully to patients table!</p>";
} catch(PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "<p style='color: orange;'>⚠ Profile picture column already exists in patients table.</p>";
    } else {
        die("<p style='color: red;'>Error adding profile picture column: " . $e->getMessage() . "</p>");
    }
}
?>
