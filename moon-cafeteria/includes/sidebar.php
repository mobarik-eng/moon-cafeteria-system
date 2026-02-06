<?php
/**
 * Sidebar Navigation
 * Moon Cafeteria Management System
 */

$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">🌙
            <?php echo SITE_NAME; ?>
        </div>
    </div>

    <ul class="sidebar-menu">
        <?php if (has_role(ROLE_ADMIN)): ?>
            <!-- Admin Menu -->
            <li>
                <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="<?php echo is_active('dashboard.php'); ?>">
                    <i>📊</i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?php echo SITE_URL; ?>/admin/products.php"
                    class="<?php echo is_active('products.php') || is_active('product_add.php') || is_active('product_edit.php'); ?>">
                    <i>🍔</i>
                    <span>Products</span>
                </a>
            </li>
            <li>
                <a href="<?php echo SITE_URL; ?>/admin/categories.php" class="<?php echo is_active('categories.php'); ?>">
                    <i>📑</i>
                    <span>Categories</span>
                </a>
            </li>
            <li>
                <a href="<?php echo SITE_URL; ?>/admin/users.php"
                    class="<?php echo is_active('users.php') || is_active('user_add.php') || is_active('user_edit.php'); ?>">
                    <i>👥</i>
                    <span>Users</span>
                </a>
            </li>
            <li>
                <a href="<?php echo SITE_URL; ?>/admin/reports.php" class="<?php echo is_active('reports.php'); ?>">
                    <i>📈</i>
                    <span>Reports</span>
                </a>
            </li>
        <?php else: ?>
            <!-- Cashier Menu -->
            <li>
                <a href="<?php echo SITE_URL; ?>/cashier/dashboard.php" class="<?php echo is_active('dashboard.php'); ?>">
                    <i>📊</i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?php echo SITE_URL; ?>/cashier/pos.php" class="<?php echo is_active('pos.php'); ?>">
                    <i>🛒</i>
                    <span>Point of Sale</span>
                </a>
            </li>
            <li>
                <a href="<?php echo SITE_URL; ?>/cashier/orders.php" class="<?php echo is_active('orders.php'); ?>">
                    <i>📋</i>
                    <span>Orders</span>
                </a>
            </li>
        <?php endif; ?>

        <li style="margin-top: auto; border-top: 1px solid var(--border-color); padding-top: 1rem;">
            <a href="<?php echo SITE_URL; ?>/auth/logout.php">
                <i>🚪</i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</aside>