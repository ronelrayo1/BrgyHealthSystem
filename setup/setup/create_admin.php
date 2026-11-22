<?php
$passwords = ['admin123', 'staff123', 'doctor123'];
foreach ($passwords as $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    echo "Password hash for '$password':<br>";
    echo $hash;
    echo "<br><br>";
}
?>
