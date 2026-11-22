<?php
abstract class DBRepository
{
    protected static $db;

    protected static function initDB()
    {
        if (!self::$db) {
            require_once __DIR__ . '/../config/database.php';
            self::$db = $pdo;
        }
    }
}
?>
