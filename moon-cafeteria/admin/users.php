<?php
/**
 * Users Management
 * Moon Cafeteria Management System
 */

require_once '../config/config.php';
$page_title = 'Users - ' . SITE_NAME;
require_once '../includes/header.php';
require_role(ROLE_ADMIN);

// Get all users
$sql = "SELECT u.*, r.role_name 
        FROM users u 
        JOIN roles r ON u.role_id = r.id 
        ORDER BY u.created_at DESC";
$users = db_fetch_all($sql);
?>

<div class="dashboard-wrapper">
    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="topbar">
            <div class="topbar-left">
                <h2>Users</h2>
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
            <?php
            $flash = get_flash();
            if ($flash):
                ?>
                <div class="alert alert-<?php echo $flash['type']; ?>">
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Users</h3>
                    <a href="user_add.php" class="btn btn-success btn-sm">+ Add User</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Full Name</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No users found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td>
                                                <?php echo $user['id']; ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($user['username']); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($user['full_name']); ?>
                                            </td>
                                            <td><span class="badge badge-info">
                                                    <?php echo htmlspecialchars($user['role_name']); ?>
                                                </span></td>
                                            <td>
                                                <?php if ($user['status'] === 'active'): ?>
                                                    <span class="badge badge-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php echo format_date($user['created_at']); ?>
                                            </td>
                                            <td>
                                                <a href="user_edit.php?id=<?php echo $user['id']; ?>"
                                                    class="btn btn-secondary btn-sm">Edit</a>
                                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                    <a href="user_delete.php?id=<?php echo $user['id']; ?>"
                                                        class="btn btn-danger btn-sm"
                                                        onclick="return confirmDelete('Are you sure you want to delete this user?')">Delete</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>