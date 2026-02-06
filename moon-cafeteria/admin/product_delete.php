<?php
/**
 * Delete Product
 * Moon Cafeteria Management System
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

require_role(ROLE_ADMIN);

$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    // Get product info
    $product = db_fetch("SELECT * FROM products WHERE id = ?", [$id]);

    if ($product) {
        // Delete image if exists
        if ($product['image'] && $product['image'] !== 'default.jpg') {
            delete_image($product['image']);
        }

        // Delete product
        if (db_query("DELETE FROM products WHERE id = ?", [$id])) {
            set_flash('success', 'Product deleted successfully');
        } else {
            set_flash('error', 'Failed to delete product');
        }
    } else {
        set_flash('error', 'Product not found');
    }
}

redirect(SITE_URL . '/admin/products.php');
?>