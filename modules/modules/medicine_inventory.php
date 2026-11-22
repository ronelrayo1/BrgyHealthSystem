<?php
require_once __DIR__ . '/../config/session_init.php';

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Session expired. Please login again.']);
    exit();
}

require_once __DIR__ . '/../config/database.php';

$userRole = $_SESSION['role'] ?? 'staff';

if (!in_array($userRole, ['admin', 'staff', 'doctor'])) {
    header('Location: index.php?page=dashboard');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        require_once __DIR__ . '/../classes/Medicine.php';
        $medicine = new Medicine($pdo);

        $data = [
            'medicine_name' => trim($_POST['medicine_name']),
            'generic_name' => trim($_POST['generic_name']),
            'category' => trim($_POST['category']),
            'quantity' => intval($_POST['quantity']),
            'unit' => trim($_POST['unit']),
            'expiry_date' => trim($_POST['expiry_date']),
            'price' => floatval($_POST['price']),
            'manufacturer' => trim($_POST['manufacturer']),
            'supplier' => trim($_POST['supplier']),
            'inventor' => trim($_POST['inventor'] ?? '')
        ];

        $result = $medicine->addMedicine($data);

        if ($result['success']) {
            $_SESSION['success'] = 'Medicine added successfully';
            header('Location: index.php?page=medicine_inventory');
            exit();
        } else {
            $_SESSION['error'] = implode(', ', $result['errors']);
        }
    }

    if ($_POST['action'] == 'delete') {
        $id = intval($_POST['id']);
        try {
            $stmt = $pdo->prepare("DELETE FROM medicine_inventory WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $_SESSION['success'] = 'Medicine deleted successfully';
            header('Location: index.php?page=medicine_inventory');
            exit();
        } catch(PDOException $e) {
            $_SESSION['error'] = 'Error deleting medicine: ' . $e->getMessage();
        }
    }

    if ($_POST['action'] == 'edit') {
        $id = intval($_POST['id']);
        $medicine_name = trim($_POST['medicine_name']);
        $generic_name = trim($_POST['generic_name']);
        $category = trim($_POST['category']);
        $quantity = intval($_POST['quantity']);
        $unit = trim($_POST['unit']);
        $expiry_date = trim($_POST['expiry_date']);
        $price = floatval($_POST['price']);
        $manufacturer = trim($_POST['manufacturer']);
        $supplier = trim($_POST['supplier']);
        $inventor = trim($_POST['inventor'] ?? '');

        try {
            $stmt = $pdo->prepare("UPDATE medicine_inventory SET medicine_name = :medicine_name, generic_name = :generic_name, category = :category, quantity = :quantity, unit = :unit, expiry_date = :expiry_date, price = :price, manufacturer = :manufacturer, supplier = :supplier, inventor = :inventor WHERE id = :id");
            $stmt->execute([
                ':medicine_name' => $medicine_name,
                ':generic_name' => $generic_name,
                ':category' => $category,
                ':quantity' => $quantity,
                ':unit' => $unit,
                ':expiry_date' => $expiry_date,
                ':price' => $price,
                ':manufacturer' => $manufacturer,
                ':supplier' => $supplier,
                ':inventor' => $inventor,
                ':id' => $id
            ]);
            $_SESSION['success'] = 'Medicine updated successfully';
            header('Location: index.php?page=medicine_inventory');
            exit();
        } catch(PDOException $e) {
            $_SESSION['error'] = 'Error updating medicine: ' . $e->getMessage();
        }
    }
}

$stmt = $pdo->query("SELECT * FROM medicine_inventory ORDER BY medicine_name ASC");
$medicines = $stmt->fetchAll();
?>



    <div class="dashboard-container">
        <?php include __DIR__ . '/../components/sidebar.php'; ?>

        <main class="dashboard-content">
            <div class="section-header">
                <h1>Medicine Inventory</h1>
                <button class="btn btn-primary" onclick="openAddModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Add Medicine
                </button>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert-error">
                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert-success">
                    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <div class="table-wrapper">
                <table>
                <thead>
                    <tr>
                        <th>Medicine Name</th>
                        <th>Generic Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Expiry Date</th>
                        <th>Price</th>
                        <th>Manufacturer</th>
                        <th>Supplier</th>
                        <th>Inventor</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($medicines)): ?>
                        <tr>
                            <td colspan="11" class="text-center">No medicines found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($medicines as $medicine): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($medicine['medicine_name']); ?></td>
                                <td><?php echo htmlspecialchars($medicine['generic_name']); ?></td>
                                <td><?php echo htmlspecialchars($medicine['category']); ?></td>
                                <td><?php echo $medicine['quantity']; ?></td>
                                <td><?php echo htmlspecialchars($medicine['unit']); ?></td>
                                <td><?php echo htmlspecialchars($medicine['expiry_date']); ?></td>
                                <td>₱<?php echo number_format($medicine['price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($medicine['manufacturer'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($medicine['supplier'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($medicine['inventor'] ?? '-'); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-edit" onclick="openEditModal(<?php echo $medicine['id']; ?>, '<?php echo htmlspecialchars($medicine['medicine_name']); ?>', '<?php echo htmlspecialchars($medicine['generic_name']); ?>', '<?php echo htmlspecialchars($medicine['category']); ?>', <?php echo $medicine['quantity']; ?>, '<?php echo htmlspecialchars($medicine['unit']); ?>', '<?php echo htmlspecialchars($medicine['expiry_date']); ?>', <?php echo $medicine['price']; ?>, '<?php echo htmlspecialchars($medicine['manufacturer'] ?? ''); ?>', '<?php echo htmlspecialchars($medicine['supplier'] ?? ''); ?>', '<?php echo htmlspecialchars($medicine['inventor'] ?? ''); ?>')">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form method="POST" class="display-inline">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $medicine['id']; ?>">
                                            <button type="submit" class="btn-delete" onclick="return confirm('Delete this medicine?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </main>
    </div>

    <!-- Add Medicine Modal -->
    <div class="modal" id="addModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Medicine</h2>
            </div>
            <form class="modal-form" method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-group full-width">
                    <label for="addMedicineName">Medicine Name</label>
                    <input type="text" name="medicine_name" id="addMedicineName" required>
                </div>
                <div class="form-group">
                    <label for="addGenericName">Generic Name</label>
                    <input type="text" name="generic_name" id="addGenericName" required>
                </div>
                <div class="form-group">
                    <label for="addCategory">Category</label>
                    <select name="category" id="addCategory" required>
                        <option value="">Select Category</option>
                        <option value="Antibiotics">Antibiotics</option>
                        <option value="Pain Relief">Pain Relief</option>
                        <option value="Vitamins">Vitamins</option>
                        <option value="Respiratory">Respiratory</option>
                        <option value="Cardiovascular">Cardiovascular</option>
                        <option value="Gastrointestinal">Gastrointestinal</option>
                        <option value="Dermatological">Dermatological</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="addQuantity">Quantity</label>
                    <input type="number" name="quantity" id="addQuantity" required min="0">
                </div>
                <div class="form-group">
                    <label for="addUnit">Unit</label>
                    <select name="unit" id="addUnit" required>
                        <option value="">Select Unit</option>
                        <option value="Tablets">Tablets</option>
                        <option value="Capsules">Capsules</option>
                        <option value="ml">ml</option>
                        <option value="mg">mg</option>
                        <option value="g">g</option>
                        <option value="Pieces">Pieces</option>
                        <option value="Bottles">Bottles</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="addExpiryDate">Expiry Date</label>
                    <input type="date" name="expiry_date" id="addExpiryDate" required>
                </div>
                <div class="form-group">
                    <label for="addPrice">Price (₱)</label>
                    <input type="number" name="price" id="addPrice" required min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label for="addManufacturer">Manufacturer</label>
                    <input type="text" name="manufacturer" id="addManufacturer">
                </div>
                <div class="form-group">
                    <label for="addSupplier">Supplier</label>
                    <input type="text" name="supplier" id="addSupplier">
                </div>
                <div class="form-group full-width">
                    <label for="addInventor">Medicine Inventor</label>
                    <input type="text" name="inventor" id="addInventor" placeholder="Enter inventor name">
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn-modal btn-cancel" onclick="closeAddModal()">Cancel</button>
                    <button type="submit" class="btn-modal btn-save">Add Medicine</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Medicine Modal -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Medicine</h2>
            </div>
            <form class="modal-form" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="editMedicineId">
                <div class="form-group full-width">
                    <label for="editMedicineName">Medicine Name</label>
                    <input type="text" name="medicine_name" id="editMedicineName" required>
                </div>
                <div class="form-group">
                    <label for="editGenericName">Generic Name</label>
                    <input type="text" name="generic_name" id="editGenericName" required>
                </div>
                <div class="form-group">
                    <label for="editCategory">Category</label>
                    <select name="category" id="editCategory" required>
                        <option value="Antibiotics">Antibiotics</option>
                        <option value="Pain Relief">Pain Relief</option>
                        <option value="Vitamins">Vitamins</option>
                        <option value="Respiratory">Respiratory</option>
                        <option value="Cardiovascular">Cardiovascular</option>
                        <option value="Gastrointestinal">Gastrointestinal</option>
                        <option value="Dermatological">Dermatological</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="editQuantity">Quantity</label>
                    <input type="number" name="quantity" id="editQuantity" required min="0">
                </div>
                <div class="form-group">
                    <label for="editUnit">Unit</label>
                    <select name="unit" id="editUnit" required>
                        <option value="Tablets">Tablets</option>
                        <option value="Capsules">Capsules</option>
                        <option value="ml">ml</option>
                        <option value="mg">mg</option>
                        <option value="g">g</option>
                        <option value="Pieces">Pieces</option>
                        <option value="Bottles">Bottles</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="editExpiryDate">Expiry Date</label>
                    <input type="date" name="expiry_date" id="editExpiryDate" required>
                </div>
                <div class="form-group">
                    <label for="editPrice">Price (₱)</label>
                    <input type="number" name="price" id="editPrice" required min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label for="editManufacturer">Manufacturer</label>
                    <input type="text" name="manufacturer" id="editManufacturer">
                </div>
                <div class="form-group">
                    <label for="editSupplier">Supplier</label>
                    <input type="text" name="supplier" id="editSupplier">
                </div>
                <div class="form-group full-width">
                    <label for="editInventor">Medicine Inventor</label>
                    <input type="text" name="inventor" id="editInventor">
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn-modal btn-cancel" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn-modal btn-save">Save Changes</button>
                </div>
            </form>
        </div>
    </div>