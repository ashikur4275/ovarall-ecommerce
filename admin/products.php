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
    $db->prepare("UPDATE products SET is_active = 0 WHERE id = ?")->execute([$id]);
    setFlash('success', 'Product deleted successfully');
    redirect('products.php');
}

// Get all products
$products = $db->query("SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - OVARALL Admin</title>
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
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="admin-sidebar">
        <div class="logo">OVA<span>RALL</span></div>
        <ul class="admin-nav">
            <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="products.php" class="active"><i class="fas fa-box"></i> Products</a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="categories.php"><i class="fas fa-tags"></i> Categories</a></li>
            <li><hr class="border-secondary"></li>
            <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Store</a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <div class="admin-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Products</h2>
            <a href="product-edit.php" class="btn btn-primary-custom">
                <i class="fas fa-plus"></i> Add Product
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
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo $product['id']; ?></td>
                                    <td>
                                        <img src="<?php echo $product['image'] ?: 'https://via.placeholder.com/50'; ?>" 
                                             alt="<?php echo $product['name']; ?>" style="width: 50px; height: 50px; object-fit: cover;">
                                    </td>
                                    <td><?php echo $product['name']; ?></td>
                                    <td><?php echo $product['category_name']; ?></td>
                                    <td>
                                        <?php if ($product['sale_price']): ?>
                                            <span class="text-decoration-line-through text-muted">৳<?php echo number_format($product['price']); ?></span>
                                            <span class="text-primary fw-bold">৳<?php echo number_format($product['sale_price']); ?></span>
                                        <?php else: ?>
                                            ৳<?php echo number_format($product['price']); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $product['stock'] < 10 ? 'danger' : 'success'; ?>">
                                            <?php echo $product['stock']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $product['is_active'] ? 'success' : 'secondary'; ?>">
                                            <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="product-edit.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="products.php?delete=<?php echo $product['id']; ?>" 
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
