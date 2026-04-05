<?php
require_once '../includes/config.php';

if (!isAdmin()) {
    setFlash('error', 'Access denied');
    redirect('../index.php');
}

$db = getDB();

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $db->prepare("UPDATE categories SET is_active = 0 WHERE id = ?")->execute([$id]);
    setFlash('success', 'Category deleted successfully');
    redirect('categories.php');
}

// Get all categories
$categories = $db->query("SELECT * FROM categories ORDER BY parent_id, sort_order")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories - OVARALL Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --primary-color: #ff6f42;
            --dark-color: #001f3f;
        }
        .admin-sidebar {
            background: var(--dark-color);
            min-height: 100vh;
            color: #fff;
            position: fixed;
            width: 250px;
        }
        .admin-sidebar .logo {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            font-size: 24px;
            font-weight: 700;
        }
        .admin-sidebar .logo span {
            color: var(--primary-color);
        }
        .admin-nav {
            list-style: none;
            padding: 0;
        }
        .admin-nav li a {
            display: block;
            padding: 15px 20px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s;
        }
        .admin-nav li a:hover,
        .admin-nav li a.active {
            background: var(--primary-color);
            color: #fff;
        }
        .admin-nav li a i {
            width: 25px;
        }
        .admin-content {
            margin-left: 250px;
            padding: 30px;
        }
        .btn-primary-custom {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff;
        }
        .btn-primary-custom:hover {
            background: #e5633a;
            border-color: #e5633a;
        }
    </style>
</head>
<body>
    <div class="admin-sidebar">
        <div class="logo">OVA<span>RALL</span></div>
        <ul class="admin-nav">
            <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="categories.php" class="active"><i class="fas fa-tags"></i> Categories</a></li>
            <li><hr class="border-secondary"></li>
            <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Store</a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <div class="admin-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Categories</h2>
            <a href="category-edit.php" class="btn btn-primary-custom">
                <i class="fas fa-plus"></i> Add Category
            </a>
        </div>
        
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Parent</th>
                                <th>Products</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td><?php echo $cat['id']; ?></td>
                                    <td>
                                        <?php if ($cat['image']): ?>
                                            <img src="../<?php echo $cat['image']; ?>" style="width: 40px; height: 40px; object-fit: cover;">
                                        <?php else: ?>
                                            <span class="text-muted">No image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $cat['name']; ?></td>
                                    <td><?php echo $cat['slug']; ?></td>
                                    <td>
                                        <?php
                                        if ($cat['parent_id']) {
                                            $parent = $db->prepare("SELECT name FROM categories WHERE id = ?");
                                            $parent->execute([$cat['parent_id']]);
                                            echo $parent->fetchColumn() ?: 'None';
                                        } else {
                                            echo '<span class="text-muted">Parent</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $countStmt = $db->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
                                        $countStmt->execute([$cat['id']]);
                                        echo $countStmt->fetchColumn();
                                        ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $cat['is_active'] ? 'success' : 'secondary'; ?>">
                                            <?php echo $cat['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="category-edit.php?id=<?php echo $cat['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="categories.php?delete=<?php echo $cat['id']; ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>