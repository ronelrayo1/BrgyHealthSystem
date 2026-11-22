<?php
require_once __DIR__ . '/../config/module_init.php';

header('Content-Type: application/json');

$userRole = $_SESSION['role'] ?? '';

if ($_POST['action'] == 'save_visitor_info') {
    if ($userRole !== 'visitor') {
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }

    $userId = $_SESSION['user_id'] ?? 0;

    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS visitor_info (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            middle_name VARCHAR(100),
            gender VARCHAR(20),
            contact_number VARCHAR(20),
            address TEXT,
            purpose VARCHAR(50),
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )");

        $checkStmt = $pdo->prepare("SELECT id FROM visitor_info WHERE user_id = :user_id");
        $checkStmt->execute([':user_id' => $userId]);
        $existing = $checkStmt->fetch();

        if ($existing) {
            $stmt = $pdo->prepare("UPDATE visitor_info SET 
                first_name = :first_name,
                last_name = :last_name,
                middle_name = :middle_name,
                gender = :gender,
                contact_number = :contact_number,
                address = :address,
                purpose = :purpose,
                notes = :notes
                WHERE user_id = :user_id");

            $stmt->execute([
                ':first_name' => $_POST['first_name'],
                ':last_name' => $_POST['last_name'],
                ':middle_name' => $_POST['middle_name'] ?? '',
                ':gender' => $_POST['gender'],
                ':contact_number' => $_POST['contact_number'],
                ':address' => $_POST['address'],
                ':purpose' => $_POST['purpose'],
                ':notes' => $_POST['notes'] ?? '',
                ':user_id' => $userId
            ]);

            echo json_encode(['success' => true, 'message' => 'Visitor information updated successfully']);
        } else {
            $stmt = $pdo->prepare("INSERT INTO visitor_info 
                (user_id, first_name, last_name, middle_name, gender, contact_number, address, purpose, notes)
                VALUES (:user_id, :first_name, :last_name, :middle_name, :gender, :contact_number, :address, :purpose, :notes)");

            $stmt->execute([
                ':user_id' => $userId,
                ':first_name' => $_POST['first_name'],
                ':last_name' => $_POST['last_name'],
                ':middle_name' => $_POST['middle_name'] ?? '',
                ':gender' => $_POST['gender'],
                ':contact_number' => $_POST['contact_number'],
                ':address' => $_POST['address'],
                ':purpose' => $_POST['purpose'],
                ':notes' => $_POST['notes'] ?? ''
            ]);

            echo json_encode(['success' => true, 'message' => 'Visitor information saved successfully']);
        }

        $_SESSION['visitor_info_completed'] = true;
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error saving visitor information: ' . $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
exit;
?>
