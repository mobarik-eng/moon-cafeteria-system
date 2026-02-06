<?php
/**
 * Add User
 * Moon Cafeteria Management System
 */

require_once '../config/config.php';
$page_title = 'Add User - ' . SITE_NAME;
require_once '../includes/header.php';
require_role(ROLE_ADMIN);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name = sanitize($_POST['full_name'] ?? '');
    $role_id = (int) ($_POST['role_id'] ?? 0);
    $status = sanitize($_POST['status'] ?? 'active');

    // Validation
    if (empty($username)) {
        $errors[] = 'Username is required';
    }
    if (empty($password)) {
        $errors[] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    } elseif ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match';
    }
    if (empty($full_name)) {
        $errors[] = 'Full name is required';
    }
    if ($role_id <= 0) {
        $errors[] = 'Please select a role';
    }

    // Check if username exists
    if (empty($errors)) {
        $existing = db_fetch("SELECT id FROM users WHERE username = ?", [$username]);
        if ($existing) {
            $errors[] = 'Username already exists';
        }
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, password, full_name, role_id, status) 
                VALUES (?, ?, ?, ?, ?)";

        if (db_query($sql, [$username, $hashed_password, $full_name, $role_id, $status])) {
            set_flash('success', 'User added successfully');
            redirect(SITE_URL . '/admin/users.php');
        } else {
            $errors[] = 'Failed to add user';
        }
    }
}
?>

<div class="dashboard-wrapper">
    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="topbar">
            <div class="topbar-left">
                <h2>Add User</h2>
            </div>
            <div class="topbar-right">
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <div style="font-weight: 600;">
                            <?php echo htmlspecialchars($_SESSION['full_name']); ?>
                        </div>
                        <div style="font-size: 0.85rem; color: var(--text-muted);">
                            <?php echo htmlspecialchars($_SESSION['role_name']); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul style="margin: 0; padding-left: 1.5rem;">
                        <?php foreach ($errors as $error): ?>
                            <li>
                                <?php echo htmlspecialchars($error); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User Information</h3>
                    <a href="users.php" class="btn btn-secondary btn-sm">← Back</a>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label class="form-label">Username *</label>
                            <input type="text" name="username" class="form-control"
                                value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="full_name" class="form-control"
                                value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Password *</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Confirm Password *</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Role *</label>
                            <select name="role_id" class="form-control" required>
                                <option value="">Select Role</option>
                                <option value="1" <?php echo (isset($_POST['role_id']) && $_POST['role_id'] == 1) ? 'selected' : ''; ?>>Admin</option>
                                <option value="2" <?php echo (isset($_POST['role_id']) && $_POST['role_id'] == 2) ? 'selected' : ''; ?>>Cashier</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="active" <?php echo (isset($_POST['status']) && $_POST['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo (isset($_POST['status']) && $_POST['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>

                        <div class="d-flex gap-1">
                            <button type="submit" class="btn btn-success">Add User</button>
                            <a href="users.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>