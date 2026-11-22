<?php
require_once __DIR__ . '/../config/module_init.php';

if ($_SESSION['role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Access denied. Please login as admin.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'delete') {
        $id = intval($_POST['id']);
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $_SESSION['success'] = 'User deleted successfully';
            header('Location: index.php?page=users');
            exit();
        } catch(PDOException $e) {
            $_SESSION['error'] = 'Error deleting user: ' . $e->getMessage();
        }
    }

    if ($_POST['action'] == 'approve' || $_POST['action'] == 'reject') {
        $id = intval($_POST['user_id'] ?? $_POST['id'] ?? 0);
        $newStatus = $_POST['action'] == 'approve' ? 'approved' : 'rejected';
        try {
            $stmt = $pdo->prepare("UPDATE users SET status = :status WHERE id = :id AND username != 'admin'");
            $stmt->execute([':status' => $newStatus, ':id' => $id]);
            $_SESSION['success'] = 'User ' . $newStatus . ' successfully';
        } catch(PDOException $e) {
            $_SESSION['error'] = 'Error updating user status: ' . $e->getMessage();
        }
        header('Location: index.php?page=users');
        exit();
    }

    if ($_POST['action'] == 'edit') {
        $id = intval($_POST['id']);
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $role = trim($_POST['role']);

        try {
            $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email, role = :role WHERE id = :id");
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':role' => $role,
                ':id' => $id
            ]);
            $_SESSION['success'] = 'User updated successfully';
            header('Location: index.php?page=users');
            exit();
        } catch(PDOException $e) {
            $_SESSION['error'] = 'Error updating user: ' . $e->getMessage();
        }
    }
}

$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

$pendingUsers = array_filter($users, function($u) { return ($u['status'] ?? 'approved') === 'pending'; });
?>



    <div class="dashboard-container">
        <?php include __DIR__ . '/../components/sidebar.php'; ?>

        <main class="dashboard-content">
            <div class="users-page">
                <div class="users-page-header">
                    <h1>User Management</h1>
                    <p>Manage all users in the system</p>
                </div>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert-box alert-error">
                        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert-box alert-success">
                        <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($pendingUsers)): ?>
                    <div class="pending-section">
                        <h2><i class="fas fa-bell"></i> Pending User Approvals (<?php echo count($pendingUsers); ?>)</h2>
                        <?php foreach ($pendingUsers as $pending): ?>
                            <div class="pending-item">
                                <div class="pending-info">
                                    <div class="pending-name"><?php echo htmlspecialchars($pending['username']); ?></div>
                                    <div class="pending-email"><?php echo htmlspecialchars($pending['email']); ?> — registered <?php echo date('M d, Y', strtotime($pending['created_at'])); ?></div>
                                </div>
                                <div class="pending-actions">
                                    <form method="POST" class="display-inline">
                                        <input type="hidden" name="action" value="approve">
                                        <input type="hidden" name="user_id" value="<?php echo $pending['id']; ?>">
                                        <button type="submit" class="btn-action btn-edit">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                    <form method="POST" class="display-inline">
                                        <input type="hidden" name="action" value="reject">
                                        <input type="hidden" name="user_id" value="<?php echo $pending['id']; ?>">
                                        <button type="submit" class="btn-action btn-delete" onclick="return confirm('Reject this user?')">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($users)): ?>
                    <div class="no-users">
                        No users found in the system
                    </div>
                <?php else: ?>
                    <div class="users-list">
                        <?php foreach ($users as $user): ?>
                            <div class="user-item">
                                <div class="user-main">
                                    <div class="user-initials">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="user-details">
                                        <h3 class="user-name"><?php echo htmlspecialchars($user['username']); ?></h3>
                                        <p class="user-email"><?php echo htmlspecialchars($user['email']); ?></p>
                                        <span class="user-role <?php echo htmlspecialchars($user['role']); ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                        <?php $userStatus = $user['status'] ?? 'approved'; ?>
                                        <?php if ($userStatus !== 'approved'): ?>
                                            <span class="user-role <?php echo $userStatus; ?>">
                                                <?php echo ucfirst($userStatus); ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php $isOnline = ($user['is_online'] ?? 0) == 1; ?>
                                        <span class="user-status <?php echo $isOnline ? 'online' : 'offline'; ?>">
                                            <i class="fas fa-circle"></i> <?php echo $isOnline ? 'Online' : 'Offline'; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="user-actions">
                                    <?php if ($user['username'] !== 'admin'): ?>
                                        <button class="btn-action btn-edit" onclick="openEditModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>', '<?php echo htmlspecialchars($user['email']); ?>', '<?php echo $user['role']; ?>')">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form method="POST" class="display-inline">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" class="btn-action btn-delete" onclick="return confirm('Delete this user?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn-action btn-protected" disabled>
                                            <i class="fas fa-shield-alt"></i> Protected
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <div class="modal" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit User</h2>
            </div>
            <form class="modal-form" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="editUserId">
                <div class="form-group">
                    <label for="editUsername">Username</label>
                    <input type="text" name="username" id="editUsername" required>
                </div>
                <div class="form-group">
                    <label for="editEmail">Email</label>
                    <input type="email" name="email" id="editEmail" required>
                </div>
                <div class="form-group">
                    <label for="editRole">Role</label>
                    <select name="role" id="editRole" required>
                        <option value="admin">Admin</option>
                        <option value="doctor">Doctor</option>
                        <option value="staff">Staff</option>
                        <option value="patient">Patient</option>
                    </select>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn-modal btn-cancel" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn-modal btn-save">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
