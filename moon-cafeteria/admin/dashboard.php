<?php
/**
 * Admin Dashboard
 * Moon Cafeteria Management System
 */

require_once '../config/config.php';
$page_title = 'Admin Dashboard - ' . SITE_NAME;
require_once '../includes/header.php';

// Require admin role
require_role(ROLE_ADMIN);

// Get statistics
$stats = [];

// Total sales today
$sql = "SELECT COALESCE(SUM(total_amount), 0) as total 
        FROM orders 
        WHERE DATE(order_date) = CURDATE() AND status = 'completed'";
$result = db_fetch($sql);
$stats['today_sales'] = $result['total'];

// Total orders today
$sql = "SELECT COUNT(*) as total 
        FROM orders 
        WHERE DATE(order_date) = CURDATE() AND status = 'completed'";
$result = db_fetch($sql);
$stats['today_orders'] = $result['total'];

// Total products
$sql = "SELECT COUNT(*) as total FROM products WHERE status = 'active'";
$result = db_fetch($sql);
$stats['total_products'] = $result['total'];

// Total users
$sql = "SELECT COUNT(*) as total FROM users WHERE status = 'active'";
$result = db_fetch($sql);
$stats['total_users'] = $result['total'];

// Recent orders
$sql = "SELECT o.*, u.full_name 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.order_date DESC 
        LIMIT 10";
$recent_orders = db_fetch_all($sql);

// Sales chart data (last 7 days)
$sql = "SELECT DATE(order_date) as date, SUM(total_amount) as total 
        FROM orders 
        WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
        AND status = 'completed'
        GROUP BY DATE(order_date) 
        ORDER BY date ASC";
$chart_data = db_fetch_all($sql);

// Sales by Category (for Circle Chart)
$sql = "SELECT c.category_name, SUM(oi.subtotal) as total
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        JOIN categories c ON p.category_id = c.id
        JOIN orders o ON oi.order_id = o.id
        WHERE o.status = 'completed'
        GROUP BY c.id, c.category_name";
$category_sales = db_fetch_all($sql);
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
                                <?php echo format_price($stats['today_sales']); ?>
                            </div>
                            <div class="stat-label">Today's Revenue</div>
                        </div>
                        <div class="stat-icon">💰</div>
                    </div>
                </div>

                <div class="stat-card purple">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value">
                                <?php echo $stats['today_orders']; ?>
                            </div>
                            <div class="stat-label">Today's Orders</div>
                        </div>
                        <div class="stat-icon">📦</div>
                    </div>
                </div>

                <div class="stat-card blue">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value">
                                <?php echo $stats['total_products']; ?>
                            </div>
                            <div class="stat-label">Total Products</div>
                        </div>
                        <div class="stat-icon">🍔</div>
                    </div>
                </div>

                <div class="stat-card orange">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value">
                                <?php echo $stats['total_users']; ?>
                            </div>
                            <div class="stat-label">Total Users</div>
                        </div>
                        <div class="stat-icon">👥</div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

<script>
    // Sales Chart (Line Chart) can be implemented here if needed

    // Category Distribution Chart (Doughnut)
    <?php if (!empty($category_sales)): ?>
        const ctx = document.getElementById('categoryChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($category_sales, 'category_name')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($category_sales, 'total')); ?>,
                    backgroundColor: [
                        '#00ff88', // Green
                        '#b565f5', // Purple
                        '#4d9fff', // Blue
                        '#ff6b6b', // Orange
                        '#ffe66d', // Yellow
                        '#6c757d'  // Grey
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#cccccc' // Light text for dark theme
                        }
                    },
                    title: {
                        display: true,
                        text: 'Sales by Category',
                        color: '#ffffff',
                        font: { size: 16 }
                    }
                }
            }
        });
    <?php endif; ?>
</script>