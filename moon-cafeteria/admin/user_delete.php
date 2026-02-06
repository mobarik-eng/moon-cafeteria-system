<?php
/**
 * Delete User
 * Moon Cafeteria Management System
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

require_role(ROLE_ADMIN);

$id = (int) ($_GET['id'] ?? 0);

// Prevent deleting own account
if ($id > 0 && $id != $_SESSION['user_id']) {
    if (db_query("DELETE FROM users WHERE id = ?", [$id])) {
        set_flash('success', 'User deleted successfully');
    } else {
        set_flash('error', 'Failed to delete user');
    }
} else {
    set_flash('error', 'Cannot delete your own account');
}

redirect(SITE_URL . '/admin/users.php');
?>