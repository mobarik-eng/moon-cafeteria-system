<?php
/**
 * Point of Sale (POS)
 * Moon Cafeteria Management System
 */

require_once '../config/config.php';
$page_title = 'Point of Sale - ' . SITE_NAME;
require_once '../includes/header.php';
require_role(ROLE_CASHIER);

// Get all active products with categories
$sql = "SELECT p.*, c.category_name 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE p.status = 'active' 
        ORDER BY c.category_name, p.product_name";
$products = db_fetch_all($sql);

// Get categories for filter
$categories = db_fetch_all("SELECT * FROM categories WHERE status = 'active' ORDER BY category_name");
?>

<div class="dashboard-wrapper">
    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="topbar">
            <div class="topbar-left">
                <h2>Point of Sale</h2>
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
            <div style="display: grid; grid-template-columns: 1fr 400px; gap: var(--spacing-md);">
                <!-- Products Section -->
                <div>
                    <!-- Category Filter -->
                    <div class="mb-2" style="display: flex; gap: var(--spacing-sm); flex-wrap: wrap;">
                        <button class="btn btn-secondary btn-sm" onclick="filterProducts('all')">All</button>
                        <?php foreach ($categories as $category): ?>
                            <button class="btn btn-secondary btn-sm"
                                onclick="filterProducts('<?php echo $category['id']; ?>')">
                                <?php echo htmlspecialchars($category['category_name']); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <!-- Products Grid -->
                    <div class="product-grid">
                        <?php foreach ($products as $product): ?>
                            <div class="product-card" data-category="<?php echo $product['category_id']; ?>"
                                onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['product_name'], ENT_QUOTES); ?>', <?php echo $product['price']; ?>)">
                                <div class="product-image"
                                    style="background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue)); display: flex; align-items: center; justify-content: center; font-size: 3rem;">
                                    <?php
                                    // Emoji based on category
                                    $emojis = [1 => '🥤', 2 => '🍔', 3 => '🍟', 4 => '🍰'];
                                    echo $emojis[$product['category_id']] ?? '🍽️';
                                    ?>
                                </div>
                                <div class="product-info">
                                    <div class="product-name">
                                        <?php echo htmlspecialchars($product['product_name']); ?>
                                    </div>
                                    <div class="product-price">
                                        <?php echo format_price($product['price']); ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">Stock:
                                        <?php echo $product['stock']; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Cart Section -->
                <div>
                    <div class="cart-section">
                        <h3 style="margin-bottom: var(--spacing-md);">🛒 Cart</h3>

                        <div id="cart-items"
                            style="max-height: 300px; overflow-y: auto; margin-bottom: var(--spacing-md);">
                            <!-- Cart items will be added here by JavaScript -->
                        </div>

                        <div class="cart-total">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 1.25rem; font-weight: 600;">Total:</span>
                                <span id="cart-total" class="total-amount">$0.00</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Payment Method</label>
                            <select id="payment-method" class="form-control">
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="mobile">Mobile Payment</option>
                            </select>
                        </div>

                        <div class="d-flex gap-1" style="flex-direction: column;">
                            <button class="btn btn-success" onclick="checkout()">
                                💳 Checkout
                            </button>
                            <button class="btn btn-danger" onclick="clearCart()">
                                🗑️ Clear Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>