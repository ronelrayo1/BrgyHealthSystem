<?php

function generateQRCode($patientId, $patientData) {
    $uploadDir = __DIR__ . '/../uploads/qr_codes/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $filename = 'qr_patient_' . $patientId . '_' . time() . '.png';
    $filepath = $uploadDir . $filename;

    $apiUrl = 'https://api.qrserver.com/v1/create-qr-code/';
    $data = urlencode($patientData);
    $size = '200x200';
    
    $qrUrl = $apiUrl . '?size=' . $size . '&data=' . $data;

    $imageData = file_get_contents($qrUrl);
    
    if ($imageData === false) {
        error_log("Failed to download QR code from API");
        return false;
    }

    if (file_put_contents($filepath, $imageData) === false) {
        error_log("Failed to save QR code image");
        return false;
    }

    return $filename;
}

function getQRCodeUrl($filename) {
    if (empty($filename)) {
        return '';
    }
    return 'uploads/qr_codes/' . $filename;
}
?>
