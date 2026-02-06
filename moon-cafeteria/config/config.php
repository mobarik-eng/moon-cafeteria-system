<?php
/**
 * Application Configuration
 * Moon Cafeteria Management System
 */

// Site settings
define('SITE_NAME', 'Moon Cafeteria');
define('SITE_URL', 'http://localhost/moon-cafeteria');

// Timezone
date_default_timezone_set('Africa/Mogadishu');

// Session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// File upload settings
define('UPLOAD_DIR', __DIR__ . '/../assets/images/products/');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Pagination
define('ITEMS_PER_PAGE', 10);

// Currency
define('CURRENCY_SYMBOL', '$');

// Roles
define('ROLE_ADMIN', 1);
define('ROLE_CASHIER', 2);
?>