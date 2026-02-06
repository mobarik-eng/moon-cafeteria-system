<?php
/**
 * Add Product
 * Moon Cafeteria Management System
 */

require_once '../config/config.php';
$page_title = 'Add Product - ' . SITE_NAME;
require_once '../includes/header.php';
require_role(ROLE_ADMIN);

// Get categories
$categories = db_fetch_all("SELECT * FROM categories WHERE status = 'active' ORDER BY category_name");

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = sanitize($_POST['product_name'] ?? '');
    $category_id = (int) ($_POST['category_id'] ?? 0);
    $description = sanitize($_POST['description'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $stock = (int) ($_POST['stock'] ?? 0);
    $status = sanitize($_POST['status'] ?? 'active');

    // Validation
    if (empty($product_name)) {
        $errors[] = 'Product name is required';
    }
    if ($category_id <= 0) {
        $errors[] = 'Please select a category';
    }
    if ($price <= 0) {
        $errors[] = 'Price must be greater than 0';
    }

    // Handle image upload
    $image_filename = 'default.jpg';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploaded = upload_image($_FILES['image']);
        if ($uploaded) {
            $image_filename = $uploaded;
        } else {
            $errors[] = 'Failed to upload image';
        }
    }

    if (empty($errors)) {
        $sql = "INSERT INTO products (category_id, product_name, description, price, image, stock, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        if (db_query($sql, [$category_id, $product_name, $description, $price, $image_filename, $stock, $status])) {
            set_flash('success', 'Product added successfully');
            redirect(SITE_URL . '/admin/products.php');
        } else {
            $errors[] = 'Failed to add product';
        }
    }
}
?>

<div class="dashboard-wrapper">
    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="topbar">
            <div class="topbar-left">
                <h2>Add Product</h2>
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
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul style="margin: 0; padding-left: 1.5rem;">
                        <?php foreach ($errors as $error): ?>
                            <li>
                                <?php echo htmlspecialchars($error); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Product Information</h3>
                    <a href="products.php" class="btn btn-secondary btn-sm">← Back</a>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="form-label">Product Name *</label>
                            <input type="text" name="product_name" class="form-control"
                                value="<?php echo htmlspecialchars($_POST['product_name'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Category *</label>
                            <select name="category_id" class="form-control" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['category_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control"
                                rows="3"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Price *</label>
                            <input type="number" name="price" class="form-control" step="0.01" min="0"
                                value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Stock</label>
                            <input type="number" name="stock" class="form-control" min="0"
                                value="<?php echo htmlspecialchars($_POST['stock'] ?? '0'); ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Product Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="active" <?php echo (isset($_POST['status']) && $_POST['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo (isset($_POST['status']) && $_POST['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>

                        <div class="d-flex gap-1">
                            <button type="submit" class="btn btn-success">Add Product</button>
                            <a href="products.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>