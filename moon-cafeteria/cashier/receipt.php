<?php
/**
 * Receipt
 * Moon Cafeteria Management System
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

require_role(ROLE_CASHIER);

$order_id = (int) ($_GET['id'] ?? 0);

// Get order details
$sql = "SELECT o.*, u.full_name as cashier_name 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE o.id = ?";
$order = db_fetch($sql, [$order_id]);

if (!$order) {
    die('Order not found');
}

// Get order items
$sql = "SELECT oi.*, p.product_name 
        FROM order_items oi 
        JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ?";
$items = db_fetch_all($sql, [$order_id]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #
        <?php echo $order_id; ?> -
        <?php echo SITE_NAME; ?>
    </title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <style>
        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
            }

            .receipt-container {
                box-shadow: none;
                border: none;
            }
        }

        .receipt-container {
            max-width: 400px;
            margin: 2rem auto;
            background: white;
            color: #000;
            padding: 2rem;
            border: 2px solid #ddd;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 1.5rem;
            border-bottom: 2px dashed #000;
            padding-bottom: 1rem;
        }

        .receipt-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .receipt-info {
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .receipt-info div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.25rem;
        }

        .receipt-items {
            margin-bottom: 1.5rem;
        }

        .receipt-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .receipt-total {
            border-top: 2px solid #000;
            padding-top: 1rem;
            margin-top: 1rem;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 1.25rem;
            font-weight: bold;
        }

        .receipt-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 2px dashed #000;
            font-size: 0.85rem;
        }
    </style>
</head>

<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <div class="receipt-title">🌙
                <?php echo SITE_NAME; ?>
            </div>
            <div style="font-size: 0.9rem;">Thank you for your purchase!</div>
        </div>

        <div class="receipt-info">
            <div>
                <span>Receipt #:</span>
                <span>
                    <?php echo $order_id; ?>
                </span>
            </div>
            <div>
                <span>Date:</span>
                <span>
                    <?php echo format_datetime($order['order_date']); ?>
                </span>
            </div>
            <div>
                <span>Cashier:</span>
                <span>
                    <?php echo htmlspecialchars($order['cashier_name']); ?>
                </span>
            </div>
            <div>
                <span>Payment:</span>
                <span>
                    <?php echo ucfirst($order['payment_method']); ?>
                </span>
            </div>
        </div>

        <div class="receipt-items">
            <div
                style="border-bottom: 1px solid #ddd; padding-bottom: 0.5rem; margin-bottom: 0.5rem; font-weight: bold;">
                <div style="display: flex; justify-content: space-between;">
                    <span>Item</span>
                    <span>Total</span>
                </div>
            </div>
            <?php foreach ($items as $item): ?>
                <div class="receipt-item">
                    <div>
                        <div>
                            <?php echo htmlspecialchars($item['product_name']); ?>
                        </div>
                        <div style="font-size: 0.8rem; color: #666;">
                            <?php echo $item['quantity']; ?> x
                            <?php echo format_price($item['price']); ?>
                        </div>
                    </div>
                    <div>
                        <?php echo format_price($item['subtotal']); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="receipt-total">
            <div class="total-row">
                <span>TOTAL:</span>
                <span>
                    <?php echo format_price($order['total_amount']); ?>
                </span>
            </div>
        </div>

        <div class="receipt-footer">
            <p>Visit us again soon!</p>
            <p style="font-size: 0.75rem; color: #666;">Powered by Moon Cafeteria System</p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin: 2rem;">
        <button onclick="window.print()" class="btn btn-primary">🖨️ Print Receipt</button>
        <a href="pos.php" class="btn btn-secondary">← Back to POS</a>
    </div>
</body>

</html>