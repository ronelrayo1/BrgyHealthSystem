<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/qr_code_config.php';

try {
    // Get all patients
    $stmt = $pdo->query("SELECT id, first_name, last_name, birth_date, qr_code FROM patients");
    $patients = $stmt->fetchAll();

    echo "Found " . count($patients) . " patients<br><br>";

    foreach ($patients as $patient) {
        echo "Patient ID: " . $patient['id'] . " - " . $patient['first_name'] . ' ' . $patient['last_name'] . "<br>";
        echo "Current QR Code: " . ($patient['qr_code'] ?? 'None') . "<br>";

        // Generate new QR code with unique data
        $qrData = json_encode([
            'id' => $patient['id'],
            'name' => $patient['first_name'] . ' ' . $patient['last_name'],
            'birth_date' => $patient['birth_date']
        ]);

        echo "QR Data: " . $qrData . "<br>";

        $qrFilename = generateQRCode($patient['id'], $qrData);

        if ($qrFilename) {
            $updateSql = "UPDATE patients SET qr_code = :qr_code WHERE id = :id";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute([
                ':qr_code' => $qrFilename,
                ':id' => $patient['id']
            ]);
            echo "✓ New QR Code generated: " . $qrFilename . "<br>";
        } else {
            echo "✗ Failed to generate QR code<br>";
        }
        echo "<br>";
    }

    echo "<br><strong>All QR codes regenerated successfully!</strong>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
