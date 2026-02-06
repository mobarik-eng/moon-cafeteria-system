<?php
/**
 * Orders History
 * Moon Cafeteria Management System
 */

require_once '../config/config.php';
$page_title = 'Orders - ' . SITE_NAME;
require_once '../includes/header.php';
require_role(ROLE_CASHIER);

$user_id = $_SESSION['user_id'];

// Get all orders for this cashier
$sql = "SELECT * FROM orders 
        WHERE user_id = ? 
        ORDER BY order_date DESC";
$orders = db_fetch_all($sql, [$user_id]);
?>

<div class="dashboard-wrapper">
    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="topbar">
            <div class="topbar-left">
                <h2>Orders History</h2>
            </div>
            <div class="topbar-right">
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <div style="font-weight: 600;">
                            <?php echo htmlspecialchars($_SESSION['full_name']); ?>
                        </div>
                        <div style="font-size: 0.85rem; color: var(--text-muted);">
                            <?php echo htmlspecialchars($_SESSION['role_name']); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Orders</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date & Time</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($orders)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No orders found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>#
                                                <?php echo $order['id']; ?>
                                            </td>
                                            <td>
                                                <?php echo format_datetime($order['order_date']); ?>
                                            </td>
                                            <td>
                                                <?php echo format_price($order['total_amount']); ?>
                                            </td>
                                            <td><span class="badge badge-info">
                                                    <?php echo ucfirst($order['payment_method']); ?>
                                                </span></td>
                                            <td>
                                                <?php if ($order['status'] === 'completed'): ?>
                                                    <span class="badge badge-success">Completed</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Cancelled</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="receipt.php?id=<?php echo $order['id']; ?>"
                                                    class="btn btn-secondary btn-sm" target="_blank">
                                                    View Receipt
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>