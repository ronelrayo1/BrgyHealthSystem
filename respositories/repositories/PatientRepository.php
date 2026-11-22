<?php
require_once 'DBRepository.php';
require_once 'IPatientRepository.php';
require_once '../classes/Patient.php';

class PatientRepository extends DBRepository implements IPatientRepository
{
    protected static $table = 'patients';

    public static function all(): array
    {
        self::initDB();
        $sql = "SELECT * FROM " . static::$table . " ORDER BY created_at DESC";
        $stmt = self::$db->query($sql);
        $data = $stmt->fetchAll();

        $result = [];
        foreach ($data as $row) {
            $result[] = new Patient(
                $row['first_name'],
                $row['last_name'],
                $row['middle_name'] ?? '',
                $row['birth_date'],
                $row['gender'],
                $row['address'],
                $row['contact_number'],
                $row['guardian_name'] ?? ''
            );
        }
        return $result;
    }

    public static function find(string $id): array
    {
        self::initDB();
        $sql = "SELECT * FROM " . static::$table . " WHERE id = :id";
        $stmt = self::$db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if ($row) {
            return [
                'id' => $row['id'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'middle_name' => $row['middle_name'] ?? '',
                'birth_date' => $row['birth_date'],
                'gender' => $row['gender'],
                'address' => $row['address'],
                'contact_number' => $row['contact_number'],
                'guardian_name' => $row['guardian_name'] ?? ''
            ];
        }
        return [];
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
}
?>
