<?php
/**
 * Products Management
 * Moon Cafeteria Management System
 */

require_once '../config/config.php';
$page_title = 'Products - ' . SITE_NAME;
require_once '../includes/header.php';
require_role(ROLE_ADMIN);

// Get all products with category info
$sql = "SELECT p.*, c.category_name 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        ORDER BY p.created_at DESC";
$products = db_fetch_all($sql);
?>

<div class="dashboard-wrapper">
    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="topbar">
            <div class="topbar-left">
                <h2>Products</h2>
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

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Products</h3>
                    <a href="product_add.php" class="btn btn-success btn-sm">+ Add Product</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($products)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No products found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td>
                                                <?php echo $product['id']; ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($product['product_name']); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($product['category_name']); ?>
                                            </td>
                                            <td>
                                                <?php echo format_price($product['price']); ?>
                                            </td>
                                            <td>
                                                <?php echo $product['stock']; ?>
                                            </td>
                                            <td>
                                                <?php if ($product['status'] === 'active'): ?>
                                                    <span class="badge badge-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="product_edit.php?id=<?php echo $product['id']; ?>"
                                                    class="btn btn-secondary btn-sm">Edit</a>
                                                <a href="product_delete.php?id=<?php echo $product['id']; ?>"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirmDelete('Are you sure you want to delete this product?')">Delete</a>
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