<?php
require_once 'Person.php';

class Medicine extends Person {

    public function __construct($pdo) {
        parent::__construct($pdo);
    }
    
    public function addMedicine($data) {
        $errors = $this->validateRequired($data, ['medicine_name', 'category', 'quantity', 'unit', 'expiry_date']);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        if (!$this->validateDate($data['expiry_date'])) {
            return ['success' => false, 'errors' => ['Invalid expiry date']];
        }

        if (!is_numeric($data['quantity']) || $data['quantity'] < 0) {
            return ['success' => false, 'errors' => ['Quantity must be a positive number']];
        }

        if (!is_numeric($data['price']) || $data['price'] < 0) {
            return ['success' => false, 'errors' => ['Price must be a positive number']];
        }

        $sql = "INSERT INTO medicine_inventory (medicine_name, generic_name, category, quantity, unit, expiry_date, manufacturer, supplier, price, inventor)
                VALUES (:medicine_name, :generic_name, :category, :quantity, :unit, :expiry_date, :manufacturer, :supplier, :price, :inventor)";

        try {
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':medicine_name' => $this->sanitize($data['medicine_name']),
                ':generic_name' => $this->sanitize($data['generic_name'] ?? ''),
                ':category' => $this->sanitize($data['category']),
                ':quantity' => $data['quantity'],
                ':unit' => $this->sanitize($data['unit']),
                ':expiry_date' => $data['expiry_date'],
                ':manufacturer' => $this->sanitize($data['manufacturer'] ?? ''),
                ':supplier' => $this->sanitize($data['supplier'] ?? ''),
                ':price' => $data['price'],
                ':inventor' => $this->sanitize($data['inventor'] ?? '')
            ]);

            if ($result) {
                return ['success' => true, 'id' => $this->pdo->lastInsertId()];
            }
            return ['success' => false, 'errors' => ['Failed to add medicine']];
        } catch(PDOException $e) {
            error_log("Medicine add error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Database error occurred']];
        }
    }
    
    public function getAllMedicines() {
        $sql = "SELECT * FROM medicine_inventory ORDER BY medicine_name ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    
    public function getMedicineById($id) {
        $sql = "SELECT * FROM medicine_inventory WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    public function searchMedicines($keyword) {
        $sql = "SELECT * FROM medicine_inventory 
                WHERE medicine_name LIKE :keyword 
                OR generic_name LIKE :keyword 
                OR category LIKE :keyword";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':keyword' => "%$keyword%"]);
        return $stmt->fetchAll();
    }
    
    public function updateMedicine($id, $data) {
        $errors = $this->validateRequired($data, ['medicine_name', 'category', 'quantity', 'unit', 'expiry_date']);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        if (!$this->validateDate($data['expiry_date'])) {
            return ['success' => false, 'errors' => ['Invalid expiry date']];
        }

        if (!is_numeric($data['quantity']) || $data['quantity'] < 0) {
            return ['success' => false, 'errors' => ['Quantity must be a positive number']];
        }

        if (!is_numeric($data['price']) || $data['price'] < 0) {
            return ['success' => false, 'errors' => ['Price must be a positive number']];
        }

        $sql = "UPDATE medicine_inventory SET
                medicine_name = :medicine_name,
                generic_name = :generic_name,
                category = :category,
                quantity = :quantity,
                unit = :unit,
                expiry_date = :expiry_date,
                manufacturer = :manufacturer,
                supplier = :supplier,
                price = :price,
                inventor = :inventor
                WHERE id = :id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':medicine_name' => $this->sanitize($data['medicine_name']),
                ':generic_name' => $this->sanitize($data['generic_name'] ?? ''),
                ':category' => $this->sanitize($data['category']),
                ':quantity' => $data['quantity'],
                ':unit' => $this->sanitize($data['unit']),
                ':expiry_date' => $data['expiry_date'],
                ':manufacturer' => $this->sanitize($data['manufacturer'] ?? ''),
                ':supplier' => $this->sanitize($data['supplier'] ?? ''),
                ':price' => $data['price'],
                ':inventor' => $this->sanitize($data['inventor'] ?? ''),
                ':id' => $id
            ]);

            if ($result) {
                return ['success' => true];
            }
            return ['success' => false, 'errors' => ['Failed to update medicine']];
        } catch(PDOException $e) {
            error_log("Medicine update error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Database error occurred']];
        }
    }

    public function deleteMedicine($id) {
        $sql = "DELETE FROM medicine_inventory WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([':id' => $id]);

            if ($result) {
                return ['success' => true];
            }
            return ['success' => false, 'errors' => ['Failed to delete medicine']];
        } catch(PDOException $e) {
            error_log("Medicine delete error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Database error occurred']];
        }
    }
    
    public function updateQuantity($id, $quantity) {
        $sql = "UPDATE medicine_inventory SET quantity = :quantity WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':quantity' => $quantity, ':id' => $id]);
    }
    
    public function getLowStockMedicines($threshold = 10) {
        $sql = "SELECT * FROM medicine_inventory WHERE quantity <= :threshold ORDER BY quantity ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':threshold' => $threshold]);
        return $stmt->fetchAll();
    }
    
    public function getExpiringMedicines($days = 30) {
        $sql = "SELECT * FROM medicine_inventory 
                WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL :days DAY) 
                AND expiry_date >= CURDATE()
                ORDER BY expiry_date ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':days' => $days]);
        return $stmt->fetchAll();
    }
    
    public function getMedicineCount() {
        $sql = "SELECT COUNT(*) as count FROM medicine_inventory";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch();
        return $result['count'];
    }
}
?>
