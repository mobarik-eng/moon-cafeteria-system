<?php
/**
 * Logout
 * Moon Cafeteria Management System
 */

require_once '../config/config.php';
require_once '../includes/session.php';

// Destroy session
destroy_session();

// Redirect to login
header('Location: ' . SITE_URL . '/auth/login.php');
exit;
?>