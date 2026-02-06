<?php
/**
 * Cashier Dashboard
 * Moon Cafeteria Management System
 */

require_once '../config/config.php';
$page_title = 'Cashier Dashboard - ' . SITE_NAME;
require_once '../includes/header.php';
require_role(ROLE_CASHIER);

// Get today's statistics for this cashier
$user_id = $_SESSION['user_id'];

$sql = "SELECT COALESCE(SUM(total_amount), 0) as total 
        FROM orders 
        WHERE DATE(order_date) = CURDATE() AND user_id = ? AND status = 'completed'";
$result = db_fetch($sql, [$user_id]);
$today_sales = $result['total'];

$sql = "SELECT COUNT(*) as total 
        FROM orders 
        WHERE DATE(order_date) = CURDATE() AND user_id = ? AND status = 'completed'";
$result = db_fetch($sql, [$user_id]);
$today_orders = $result['total'];

// Recent orders
$sql = "SELECT * FROM orders 
        WHERE user_id = ? 
        ORDER BY order_date DESC 
        LIMIT 5";
$recent_orders = db_fetch_all($sql, [$user_id]);
?>

<div class="dashboard-wrapper">
    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="topbar">
            <div class="topbar-left">
                <h2>Dashboard</h2>
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
            <?php
            $flash = get_flash();
            if ($flash):
                ?>
                <div class="alert alert-<?php echo $flash['type']; ?>">
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card green">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value">
                                <?php echo format_price($today_sales); ?>
                            </div>
                            <div class="stat-label">Today's Sales</div>
                        </div>
                        <div class="stat-icon">💰</div>
                    </div>
                </div>

                <div class="stat-card blue">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value">
                                <?php echo $today_orders; ?>
                            </div>
                            <div class="stat-label">Today's Orders</div>
                        </div>
                        <div class="stat-icon">📦</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-2">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <a href="pos.php" class="btn btn-success" style="font-size: 1.1rem; padding: 1rem 2rem;">
                        🛒 Open Point of Sale
                    </a>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Orders</h3>
                    <a href="orders.php" class="btn btn-secondary btn-sm">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date & Time</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_orders)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No orders yet</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recent_orders as $order): ?>
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