<?php
/**
 * Session Management
 * Moon Cafeteria Management System
 * 
 * Handles secure session management with protection against hijacking
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 * 
 * @return bool
 */
function is_logged_in()
{
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

/**
 * Check if user has specific role
 * 
 * @param int $role_id Role ID to check
 * @return bool
 */
function has_role($role_id)
{
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] == $role_id;
}

/**
 * Require login - redirect if not logged in
 */
function require_login()
{
    if (!is_logged_in()) {
        header('Location: ' . SITE_URL . '/auth/login.php');
        exit;
    }
}

/**
 * Require specific role - redirect if unauthorized
 * 
 * @param int $role_id Required role ID
 */
function require_role($role_id)
{
    require_login();
    if (!has_role($role_id)) {
        header('Location: ' . SITE_URL . '/index.php');
        exit;
    }
}

/**
 * Set user session data
 * 
 * @param array $user User data from database
 */
function set_user_session($user)
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['role_id'] = $user['role_id'];
    $_SESSION['role_name'] = $user['role_name'];
    $_SESSION['last_activity'] = time();
}

/**
 * Destroy user session
 */
function destroy_session()
{
    $_SESSION = [];
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    session_destroy();
}

/**
 * Check session timeout (30 minutes)
 */
function check_session_timeout()
{
    $timeout = 1800; // 30 minutes
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        destroy_session();
        header('Location: ' . SITE_URL . '/auth/login.php?timeout=1');
        exit;
    }
    $_SESSION['last_activity'] = time();
}

// Check timeout on every page load if logged in
if (is_logged_in()) {
    check_session_timeout();
}
?>