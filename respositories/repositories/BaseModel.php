<?php
abstract class BaseModel
{
    protected static $table;
    protected static $db;

    protected static function initDB()
    {
        if (!self::$db) {
            require_once __DIR__ . '/../config/database.php';
            self::$db = $pdo;
        }
    }

    public static function all(): array
    {
        self::initDB();
        $sql = "SELECT * FROM " . static::$table . " ORDER BY created_at DESC";
        $stmt = self::$db->query($sql);
        return $stmt->fetchAll();
    }

    public static function find(string $id): array
    {
        self::initDB();
        $sql = "SELECT * FROM " . static::$table . " WHERE id = :id";
        $stmt = self::$db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: [];
    }

    public static function update(array $data): array
    {
        self::initDB();
        $id = $data['id'];
        unset($data['id']);

        $setClause = [];
        foreach ($data as $key => $value) {
            $setClause[] = "$key = :$key";
        }
        $setClause = implode(', ', $setClause);

        $sql = "UPDATE " . static::$table . " SET $setClause WHERE id = :id";
        $data[':id'] = $id;

        try {
            $stmt = self::$db->prepare($sql);
            $result = $stmt->execute($data);

            if ($result) {
                return ['success' => true, 'message' => 'Record updated successfully'];
            }
            return ['success' => false, 'message' => 'Failed to update record'];
        } catch (PDOException $e) {
            error_log("Update error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    public static function delete(string $id): void
    {
        self::initDB();
        $sql = "DELETE FROM " . static::$table . " WHERE id = :id";
        $stmt = self::$db->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    public static function create(array $data): array
    {
        self::initDB();
        $columns = array_keys($data);
        $placeholders = array_map(function($col) { return ":$col"; }, $columns);

        $sql = "INSERT INTO " . static::$table . " (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";

        try {
            $stmt = self::$db->prepare($sql);
            $result = $stmt->execute($data);

            if ($result) {
                return ['success' => true, 'id' => self::$db->lastInsertId()];
            }
            return ['success' => false, 'message' => 'Failed to create record'];
        } catch (PDOException $e) {
            error_log("Create error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    public static function search(string $keyword, array $columns = []): array
    {
        self::initDB();
        if (empty($columns)) {
            $columns = ['*'];
        }

        $whereClause = [];
        foreach ($columns as $column) {
            if ($column !== '*') {
                $whereClause[] = "$column LIKE :keyword";
            }
        }

        if (empty($whereClause)) {
            return self::all();
        }

        $sql = "SELECT * FROM " . static::$table . " WHERE " . implode(' OR ', $whereClause);
        $stmt = self::$db->prepare($sql);
        $stmt->execute([':keyword' => "%$keyword%"]);
        return $stmt->fetchAll();
    }
}
?>
