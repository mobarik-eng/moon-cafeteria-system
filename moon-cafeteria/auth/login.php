<?php
/**
 * Login Page
 * Moon Cafeteria Management System
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect(SITE_URL . '/index.php');
}

$error = '';
$timeout_message = '';

// Check for timeout
if (isset($_GET['timeout'])) {
    $timeout_message = 'Your session has expired. Please login again.';
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        // Query user from database
        $sql = "SELECT u.*, r.role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                WHERE u.username = ? AND u.status = 'active'";

        $user = db_fetch($sql, [$username]);

        // DEBUGGING OUTPUT
        echo "<div style='background: #fff3cd; padding: 10px; border: 1px solid #ffeeba; margin-bottom: 20px; color: #856404;'>";
        echo "<strong>Debug Info:</strong><br>";
        echo "Attempted Username: " . htmlspecialchars($username) . "<br>";
        if ($user) {
            echo "User Found: Yes<br>";
            echo "User ID: " . $user['id'] . "<br>";
            echo "Role ID: " . $user['role_id'] . "<br>";
            echo "Status: " . $user['status'] . "<br>";
            echo "Stored Hash: " . substr($user['password'], 0, 10) . "...<br>";
            $verify = password_verify($password, $user['password']);
            echo "Password Verify Result: " . ($verify ? "TRUE" : "FALSE") . "<br>";
        } else {
            echo "User Found: NO (Check username or status)<br>";
        }
        echo "</div>";

        // Verify password
        if ($user && password_verify($password, $user['password'])) {
            // Set session
            set_user_session($user);

            // Redirect based on role
            if ($user['role_id'] == ROLE_ADMIN) {
                redirect(SITE_URL . '/admin/dashboard.php');
            } else {
                redirect(SITE_URL . '/cashier/dashboard.php');
            }
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login -
        <?php echo SITE_NAME; ?>
    </title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>

<body>
    <div class="login-wrapper">
        <div class="login-box">
            <div class="login-header">
                <h1>🌙
                    <?php echo SITE_NAME; ?>
                </h1>
                <p>Please login to continue</p>
            </div>

            <?php if ($timeout_message): ?>
                <div class="alert alert-warning">
                    <?php echo $timeout_message; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Enter your username"
                        value="<?php echo htmlspecialchars($username ?? ''); ?>" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter your password"
                        required>
                </div>

                <button type="submit" class="btn btn-primary">
                    Login
                </button>
            </form>

            <div
                style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color); color: var(--text-muted); font-size: 0.85rem;">
                <p style="margin-bottom: 0.5rem;"><strong>Demo Credentials:</strong></p>
                <p style="margin-bottom: 0.25rem;">Admin: <code
                        style="background: var(--bg-secondary); padding: 0.25rem 0.5rem; border-radius: 4px;">admin / admin123</code>
                </p>
                <p>Cashier: <code
                        style="background: var(--bg-secondary); padding: 0.25rem 0.5rem; border-radius: 4px;">cashier / cashier123</code>
                </p>
            </div>
        </div>
    </div>
</body>

</html>