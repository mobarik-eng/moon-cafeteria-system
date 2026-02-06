<?php
/**
 * Common Functions
 * Moon Cafeteria Management System
 */

/**
 * Sanitize input data
 * 
 * @param string $data Input data
 * @return string Sanitized data
 */
function sanitize($data)
{
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 * 
 * @param string $email Email address
 * @return bool
 */
function validate_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generate CSRF token
 * 
 * @return string
 */
function generate_csrf_token()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * 
 * @param string $token Token to verify
 * @return bool
 */
function verify_csrf_token($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Format price
 * 
 * @param float $price Price value
 * @return string Formatted price
 */
function format_price($price)
{
    return CURRENCY_SYMBOL . number_format($price, 2);
}

/**
 * Format date
 * 
 * @param string $date Date string
 * @param string $format Date format
 * @return string Formatted date
 */
function format_date($date, $format = 'M d, Y')
{
    return date($format, strtotime($date));
}

/**
 * Format datetime
 * 
 * @param string $datetime Datetime string
 * @param string $format Datetime format
 * @return string Formatted datetime
 */
function format_datetime($datetime, $format = 'M d, Y h:i A')
{
    return date($format, strtotime($datetime));
}

/**
 * Upload image file
 * 
 * @param array $file File from $_FILES
 * @param string $directory Upload directory
 * @return string|false Filename on success, false on failure
 */
function upload_image($file, $directory = UPLOAD_DIR)
{
    // Check if file was uploaded
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return false;
    }

    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return false;
    }

    // Check file extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        return false;
    }

    // Generate unique filename
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $directory . $filename;

    // Create directory if it doesn't exist
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filename;
    }

    return false;
}

/**
 * Delete image file
 * 
 * @param string $filename Filename to delete
 * @param string $directory Directory path
 * @return bool
 */
function delete_image($filename, $directory = UPLOAD_DIR)
{
    $filepath = $directory . $filename;
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    return false;
}

/**
 * Set flash message
 * 
 * @param string $type Message type (success, error, warning, info)
 * @param string $message Message text
 */
function set_flash($type, $message)
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 * 
 * @return array|null
 */
function get_flash()
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Redirect to URL
 * 
 * @param string $url URL to redirect to
 */
function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

/**
 * Get current page name
 * 
 * @return string
 */
function current_page()
{
    return basename($_SERVER['PHP_SELF']);
}

/**
 * Check if current page is active
 * 
 * @param string $page Page name
 * @return string CSS class if active
 */
function is_active($page)
{
    return current_page() === $page ? 'active' : '';
}
?>