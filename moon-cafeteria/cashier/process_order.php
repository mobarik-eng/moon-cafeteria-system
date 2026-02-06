<?php
/**
 * Process Order (AJAX Handler)
 * Moon Cafeteria Management System
 */

header('Content-Type: application/json');

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

require_role(ROLE_CASHIER);

// Get JSON data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || empty($data['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$cart = $data['cart'];
$payment_method = $data['payment_method'] ?? 'cash';
$user_id = $_SESSION['user_id'];

// Calculate total
$total_amount = 0;
foreach ($cart as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

try {
    // Start transaction
    $pdo->beginTransaction();

    // Insert order
    $sql = "INSERT INTO orders (user_id, total_amount, payment_method, status) 
            VALUES (?, ?, ?, 'completed')";
    db_query($sql, [$user_id, $total_amount, $payment_method]);

    $order_id = db_last_id();

    // Insert order items
    foreach ($cart as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) 
                VALUES (?, ?, ?, ?, ?)";
        db_query($sql, [$order_id, $item['id'], $item['quantity'], $item['price'], $subtotal]);

        // Update product stock
        $sql = "UPDATE products SET stock = stock - ? WHERE id = ?";
        db_query($sql, [$item['quantity'], $item['id']]);
    }

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Order completed successfully',
        'order_id' => $order_id
    ]);

} catch (Exception $e) {
    // Rollback on error
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Failed to process order: ' . $e->getMessage()
    ]);
}
?>