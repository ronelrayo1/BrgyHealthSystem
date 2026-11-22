<?php
$cssFile = __DIR__ . '/../design/style.css';
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

$allStyles = [];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    
    $content = file_get_contents($file);
    
    preg_match_all('/<style>(.*?)<\/style>/s', $content, $matches);
    
    if (!empty($matches[1])) {
        foreach ($matches[1] as $style) {
            $style = trim($style);
            if (!empty($style)) {
                $allStyles[] = "\n/* Extracted from " . basename($file) . " */\n" . $style;
            }
        }
    }
}

echo "Found " . count($allStyles) . " style blocks\n";

$cssContent = file_get_contents($cssFile);
$cssContent .= "\n\n/* ======================================= */\n";
$cssContent .= "/* EXTRACTED INLINE STYLES */\n";
$cssContent .= "/* ======================================= */\n";

foreach ($allStyles as $style) {
    $cssContent .= $style . "\n\n";
}

file_put_contents($cssFile, $cssContent);

echo "Styles appended to $cssFile\n";
?>
