<?php
require_once __DIR__ . '/../config/database.php';

try {
    $pdo->exec('ALTER TABLE patients ADD COLUMN guardian_name VARCHAR(100) AFTER contact_number');
    echo "guardian_name column added successfully\n";
} catch(PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') === false) {
        echo "Error adding guardian_name: " . $e->getMessage() . "\n";
    } else {
        echo "guardian_name column already exists\n";
    }
}

try {
    $pdo->exec('ALTER TABLE patients ADD COLUMN child_name VARCHAR(100) AFTER guardian_name');
    echo "child_name column added successfully\n";
} catch(PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') === false) {
        echo "Error adding child_name: " . $e->getMessage() . "\n";
    } else {
        echo "child_name column already exists\n";
    }
}
?>
