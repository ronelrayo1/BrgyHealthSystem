<?php

require_once __DIR__ . '/../config/database.php';

try {
    $checkColumn = $pdo->query("SHOW COLUMNS FROM patients LIKE 'qr_code'");
    if ($checkColumn->rowCount() > 0) {
        echo "✓ qr_code column already exists in patients table.<br>";
    } else {
        $sql = "ALTER TABLE patients ADD COLUMN qr_code VARCHAR(255) NULL AFTER profile_picture";
        $pdo->exec($sql);
        echo "✓ Successfully added qr_code column to patients table.<br>";
    }

    echo "<br>Database update completed successfully!";
    echo "<br><a href='../index.php'>Return to Dashboard</a>";

} catch(PDOException $e) {
    echo "✗ Error: " . $e->getMessage();
}
?>
