<?php
/**
 * Reset System Data
 * Moon Cafeteria Management System
 * 
 * WARNING: This script deletes all business data!
 */

require_once 'config/config.php';
require_once 'config/database.php';

// Check if user confirmed
if (!isset($_POST['confirm'])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reset System Data</title>
        <link rel="stylesheet" href="assets/css/style.css">
        <style>
            body {
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100vh;
                margin: 0;
            }

            .confirm-box {
                background: var(--bg-card);
                padding: 2rem;
                border-radius: 10px;
                text-align: center;
                max-width: 500px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .warning-icon {
                font-size: 3rem;
                margin-bottom: 1rem;
            }
        </style>
    </head>

    <body>
        <div class="confirm-box">
            <div class="warning-icon">⚠️</div>
            <h2>Reset System Data?</h2>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">
                This will delete **ALL**:
                <br>- Products
                <br>- Categories
                <br>- Orders & Sales History
                <br><br>
                <strong>Admin and Cashier accounts will be KEPT.</strong>
                <br>This action cannot be undone.
            </p>
            <form method="POST">
                <input type="hidden" name="confirm" value="1">
                <button type="submit" class="btn btn-danger">Yes, Delete Everything</button>
                <a href="index.php" class="btn btn-secondary" style="margin-left: 10px;">Cancel</a>
            </form>
        </div>
    </body>

    </html>
    <?php
    exit;
}

try {
    // Disable foreign key checks to allow truncation
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    // Clear tables
    $tables = ['order_items', 'orders', 'products', 'categories'];
    foreach ($tables as $table) {
        $pdo->exec("TRUNCATE TABLE $table");
    }

    // Re-enable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    // Optional: Delete product images
    $files = glob(UPLOAD_DIR . '*');
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== 'default.jpg') {
            unlink($file);
        }
    }

    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Reset Complete</title>
        <link rel="stylesheet" href="assets/css/style.css">
        <meta http-equiv="refresh" content="3;url=admin/dashboard.php">
    </head>

    <body style="display: flex; align-items: center; justify-content: center; height: 100vh;">
        <div class="card" style="text-align: center; padding: 2rem;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">✅</div>
            <h2>System Reset Successfully!</h2>
            <p>All demo data has been cleared.</p>
            <p>Redirecting to dashboard...</p>
            <a href="admin/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
        </div>
    </body>

    </html>
    <?php

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>