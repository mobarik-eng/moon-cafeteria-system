<?php
/**
 * Sales Reports
 * Moon Cafeteria Management System
 */

require_once '../config/config.php';
$page_title = 'Sales Reports - ' . SITE_NAME;
require_once '../includes/header.php';
require_role(ROLE_ADMIN);

// Get filter parameters
$filter = $_GET['filter'] ?? 'today';
$start_date = $_GET['start_date'] ?? date('Y-m-d');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Build query based on filter
switch ($filter) {
    case 'today':
        $date_condition = "DATE(order_date) = CURDATE()";
        $title = "Today's Sales";
        break;
    case 'week':
        $date_condition = "YEARWEEK(order_date) = YEARWEEK(CURDATE())";
        $title = "This Week's Sales";
        break;
    case 'month':
        $date_condition = "YEAR(order_date) = YEAR(CURDATE()) AND MONTH(order_date) = MONTH(CURDATE())";
        $title = "This Month's Sales";
        break;
    case 'custom':
        $date_condition = "DATE(order_date) BETWEEN '$start_date' AND '$end_date'";
        $title = "Sales Report ($start_date to $end_date)";
        break;
    default:
        $date_condition = "DATE(order_date) = CURDATE()";
        $title = "Today's Sales";
}

// Get summary statistics
$sql = "SELECT 
            COUNT(*) as total_orders,
            COALESCE(SUM(total_amount), 0) as total_sales,
            COALESCE(AVG(total_amount), 0) as avg_order
        FROM orders 
        WHERE $date_condition AND status = 'completed'";
$summary = db_fetch($sql);

// Get orders
$sql = "SELECT o.*, u.full_name 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE $date_condition AND o.status = 'completed'
        ORDER BY o.order_date DESC";
$orders = db_fetch_all($sql);

// Get top products
$sql = "SELECT p.product_name, c.category_name, 
               SUM(oi.quantity) as total_quantity,
               SUM(oi.subtotal) as total_revenue
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        JOIN categories c ON p.category_id = c.id
        JOIN orders o ON oi.order_id = o.id
        WHERE $date_condition AND o.status = 'completed'
        GROUP BY p.id, p.product_name, c.category_name
        ORDER BY total_revenue DESC
        LIMIT 10";
$top_products = db_fetch_all($sql);
?>

<div class="dashboard-wrapper">
    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="topbar">
            <div class="topbar-left">
                <h2>Sales Reports</h2>
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
            <!-- Filter Form -->
            <div class="card mb-2">
                <div class="card-body">
                    <form method="GET" class="d-flex gap-1 align-center">
                        <select name="filter" class="form-control" onchange="toggleCustomDates(this.value)"
                            style="max-width: 200px;">
                            <option value="today" <?php echo $filter === 'today' ? 'selected' : ''; ?>>Today</option>
                            <option value="week" <?php echo $filter === 'week' ? 'selected' : ''; ?>>This Week</option>
                            <option value="month" <?php echo $filter === 'month' ? 'selected' : ''; ?>>This Month
                            </option>
                            <option value="custom" <?php echo $filter === 'custom' ? 'selected' : ''; ?>>Custom Range
                            </option>
                        </select>

                        <div id="custom-dates"
                            style="display: <?php echo $filter === 'custom' ? 'flex' : 'none'; ?>; gap: var(--spacing-sm);">
                            <input type="date" name="start_date" class="form-control" value="<?php echo $start_date; ?>"
                                style="max-width: 180px;">
                            <input type="date" name="end_date" class="form-control" value="<?php echo $end_date; ?>"
                                style="max-width: 180px;">
                        </div>

                        <button type="submit" class="btn btn-primary">Generate Report</button>
                        <button type="button" class="btn btn-secondary" onclick="window.print()">🖨️ Print</button>
                    </form>
                </div>
            </div>

            <script>
                function toggleCustomDates(value) {
                    document.getElementById('custom-dates').style.display = value === 'custom' ? 'flex' : 'none';
                }
            </script>

            <!-- Summary Cards -->
            <div class="stats-grid">
                <div class="stat-card green">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value">
                                <?php echo format_price($summary['total_sales']); ?>
                            </div>
                            <div class="stat-label">Total Sales</div>
                        </div>
                        <div class="stat-icon">💰</div>
                    </div>
                </div>

                <div class="stat-card blue">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value">
                                <?php echo $summary['total_orders']; ?>
                            </div>
                            <div class="stat-label">Total Orders</div>
                        </div>
                        <div class="stat-icon">📦</div>
                    </div>
                </div>

                <div class="stat-card purple">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value">
                                <?php echo format_price($summary['avg_order']); ?>
                            </div>
                            <div class="stat-label">Average Order</div>
                        </div>
                        <div class="stat-icon">📊</div>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="card mb-2">
                <div class="card-header">
                    <h3 class="card-title">Top Selling Products</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Quantity Sold</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($top_products)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No data available</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($top_products as $product): ?>
                                        <tr>
                                            <td>
                                                <?php echo htmlspecialchars($product['product_name']); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($product['category_name']); ?>
                                            </td>
                                            <td>
                                                <?php echo $product['total_quantity']; ?>
                                            </td>
                                            <td>
                                                <?php echo format_price($product['total_revenue']); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Orders List -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <?php echo $title; ?>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Cashier</th>
                                    <th>Date & Time</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($orders)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No orders found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>#
                                                <?php echo $order['id']; ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($order['full_name']); ?>
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