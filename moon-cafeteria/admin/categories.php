<?php
/**
 * Categories Management
 * Moon Cafeteria Management System
 */

require_once '../config/config.php';
$page_title = 'Categories - ' . SITE_NAME;
require_once '../includes/header.php';
require_role(ROLE_ADMIN);

// Handle add/edit category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $category_name = sanitize($_POST['category_name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $status = sanitize($_POST['status'] ?? 'active');

    if (!empty($category_name)) {
        if ($id > 0) {
            // Update
            $sql = "UPDATE categories SET category_name = ?, description = ?, status = ? WHERE id = ?";
            if (db_query($sql, [$category_name, $description, $status, $id])) {
                set_flash('success', 'Category updated successfully');
            }
        } else {
            // Insert
            $sql = "INSERT INTO categories (category_name, description, status) VALUES (?, ?, ?)";
            if (db_query($sql, [$category_name, $description, $status])) {
                set_flash('success', 'Category added successfully');
            }
        }
        redirect(SITE_URL . '/admin/categories.php');
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if (db_query("DELETE FROM categories WHERE id = ?", [$id])) {
        set_flash('success', 'Category deleted successfully');
    }
    redirect(SITE_URL . '/admin/categories.php');
}

// Get all categories
$categories = db_fetch_all("SELECT * FROM categories ORDER BY category_name");

// Get category for editing
$edit_category = null;
if (isset($_GET['edit'])) {
    $edit_category = db_fetch("SELECT * FROM categories WHERE id = ?", [(int) $_GET['edit']]);
}
?>

<div class="dashboard-wrapper">
    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="topbar">
            <div class="topbar-left">
                <h2>Categories</h2>
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

            <div style="display: grid; grid-template-columns: 1fr 400px; gap: var(--spacing-md);">
                <!-- Categories List -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">All Categories</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Category Name</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($categories)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No categories found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($categories as $category): ?>
                                            <tr>
                                                <td>
                                                    <?php echo $category['id']; ?>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($category['description']); ?>
                                                </td>
                                                <td>
                                                    <?php if ($category['status'] === 'active'): ?>
                                                        <span class="badge badge-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="?edit=<?php echo $category['id']; ?>"
                                                        class="btn btn-secondary btn-sm">Edit</a>
                                                    <a href="?delete=<?php echo $category['id']; ?>"
                                                        class="btn btn-danger btn-sm"
                                                        onclick="return confirmDelete('Delete this category?')">Delete</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Add/Edit Form -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <?php echo $edit_category ? 'Edit' : 'Add'; ?> Category
                        </h3>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <?php if ($edit_category): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_category['id']; ?>">
                            <?php endif; ?>

                            <div class="form-group">
                                <label class="form-label">Category Name *</label>
                                <input type="text" name="category_name" class="form-control"
                                    value="<?php echo htmlspecialchars($edit_category['category_name'] ?? ''); ?>"
                                    required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control"
                                    rows="3"><?php echo htmlspecialchars($edit_category['description'] ?? ''); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control">
                                    <option value="active" <?php echo (isset($edit_category['status']) && $edit_category['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo (isset($edit_category['status']) && $edit_category['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>

                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-success">
                                    <?php echo $edit_category ? 'Update' : 'Add'; ?> Category
                                </button>
                                <?php if ($edit_category): ?>
                                    <a href="categories.php" class="btn btn-secondary">Cancel</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>