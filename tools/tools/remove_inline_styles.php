<?php
$files = [
    __DIR__ . '/../modules/patients.php',
    __DIR__ . '/../modules/users.php',
    __DIR__ . '/../modules/consultations.php',
    __DIR__ . '/../modules/medical_records.php',
    __DIR__ . '/../modules/medicine_inventory.php',
    __DIR__ . '/../modules/patient_profiles.php',
    __DIR__ . '/../pages/dashboard.php',
    __DIR__ . '/../pages/forgot_password.php',
    __DIR__ . '/../pages/my_records.php',
    __DIR__ . '/../pages/complete_profile.php',
    __DIR__ . '/../pages/appointments.php',
];

$removedCount = 0;

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    
    $content = file_get_contents($file);
    $original = $content;
    
    $content = preg_replace('/<style>.*?<\/style>/s', '', $content);
    
    if ($content !== $original) {
        file_put_contents($file, $content);
        $removedCount++;
        echo "Removed styles from: " . basename($file) . "\n";
    }
}

echo "\nTotal files updated: $removedCount\n";
?>
