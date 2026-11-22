<?php

require_once '../config/database.php';

echo "<h2>Database Connection Test</h2>";

try {
    echo "<p style='color: green; font-weight: bold;'>✓ Database connection successful!</p>";
    echo "<p><strong>Database:</strong> " . $dbname . "</p>";
    echo "<p><strong>Host:</strong> " . $host . "</p>";


    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

    echo "<h3>Tables in Database:</h3>";
    if (empty($tables)) {
        echo "<p style='color: red;'>No tables found. Please run <a href='setup_database.php'>setup_database.php</a> to create the database.</p>";
    } else {
        echo "<ul>";
        foreach ($tables as $table) {
            $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            echo "<li><strong>$table</strong> - $count records</li>";
        }
        echo "</ul>";
    }


    echo "<h3>Database Query Test:</h3>";
    try {
        $result = $pdo->query("SELECT 1")->fetch();
        echo "<p style='color: green;'>✓ Query execution successful!</p>";
    } catch (PDOException $e) {
        echo "<p style='color: red;'>✗ Query failed: " . $e->getMessage() . "</p>";
    }

} catch(PDOException $e) {
    echo "<p style='color: red; font-weight: bold;'>✗ Database connection failed!</p>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration in config/database.php</p>";
}
?>
