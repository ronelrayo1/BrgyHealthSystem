<?php


$host = 'localhost';
$username = 'root';
$password = '';

try {

    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>Database Setup for Barangay Health Center Records System</h2>";
    echo "<p>Starting database setup...</p>";


    $sqlFile = __DIR__ . '/../database/database_setup.sql';
    if (!file_exists($sqlFile)) {
        die("<p style='color: red;'>Error: database_setup.sql file not found!</p>");
    }

    $sql = file_get_contents($sqlFile);


    $pdo->exec($sql);

    echo "<p style='color: green; font-weight: bold;'>✓ Database setup completed successfully!</p>";
    echo "<h3>Default User Accounts:</h3>";
    echo "<ul>";
    echo "<li><strong>Admin:</strong> username: <code>admin</code>, password: <code>admin123</code></li>";
    echo "<li><strong>Staff:</strong> username: <code>staff</code>, password: <code>staff123</code></li>";
    echo "<li><strong>Doctor:</strong> username: <code>doctor</code>, password: <code>doctor123</code></li>";
    echo "</ul>";
    echo "<p><a href='../login.php'>Go to Login Page</a></p>";
    echo "<p style='color: #666; font-size: 12px;'>Delete this file after setup for security.</p>";

} catch(PDOException $e) {
    die("<p style='color: red;'>Error setting up database: " . $e->getMessage() . "</p>");
}
?>
