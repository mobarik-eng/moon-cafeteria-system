<?php
/**
 * Database Configuration
 * Moon Cafeteria Management System
 * 
 * This file handles database connection using PDO with error handling
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'moon_cafeteria');

// Create database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

/**
 * Execute a prepared statement and return results
 * 
 * @param string $sql SQL query with placeholders
 * @param array $params Parameters to bind
 * @return PDOStatement
 */
function db_query($sql, $params = [])
{
    global $pdo;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Database query error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get single row from database
 * 
 * @param string $sql SQL query
 * @param array $params Parameters
 * @return array|false
 */
function db_fetch($sql, $params = [])
{
    $stmt = db_query($sql, $params);
    return $stmt ? $stmt->fetch() : false;
}

/**
 * Get all rows from database
 * 
 * @param string $sql SQL query
 * @param array $params Parameters
 * @return array
 */
function db_fetch_all($sql, $params = [])
{
    $stmt = db_query($sql, $params);
    return $stmt ? $stmt->fetchAll() : [];
}

/**
 * Get last inserted ID
 * 
 * @return string
 */
function db_last_id()
{
    global $pdo;
    return $pdo->lastInsertId();
}
?>