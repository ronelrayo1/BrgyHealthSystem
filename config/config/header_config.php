<?php

$scriptPath = $_SERVER['SCRIPT_NAME'];
if (strpos($scriptPath, '/modules/') !== false) {
    $basePath = '../';
} else {
    $basePath = '';
}
?>
