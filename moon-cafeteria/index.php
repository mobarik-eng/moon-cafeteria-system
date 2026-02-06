<?php
/**
 * Index Page
 * Moon Cafeteria Management System
 * 
 * Redirects to login or dashboard based on session
 */

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/session.php';

// Redirect based on login status
if (is_logged_in()) {
    // Redirect based on role
    if (has_role(ROLE_ADMIN)) {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: cashier/dashboard.php');
    }
} else {
    header('Location: auth/login.php');
}
exit;
?>