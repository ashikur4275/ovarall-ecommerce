<?php
require_once '../includes/config.php';

// Check if admin
if (!isAdmin()) {
    setFlash('error', 'Access denied');
    redirect('../index.php');
}

$pageTitle = 'Admin Dashboard';

$db = getDB();

// Get statistics
$totalProducts = $db->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn();
$totalOrders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalUsers = $db->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$totalSales = $db->query("SELECT SUM(final_amount) FROM orders WHERE status = 'delivered'")->fetchColumn() ?: 0;

// Get recent orders
$recentOrders = $db->query("SELECT o.*, u.name as user_name 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC 
    LIMIT 10")->fetchAll();

// Get low stock products
$lowStock = $db->query("SELECT * FROM products WHERE stock < 10 AND is_active = 1 LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo SITE_NAME; ?> Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #ff6f42;
            --dark-color: #001f3f;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
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
        .stat-card {
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .stat-card h3 {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        .stat-card p {
            color: #6c757d;
            margin-bottom: 0;
        }
        .stat-card i {
            font-size: 40px;
            color: rgba(255, 111, 66, 0.2);
        }
    </style>
</head>
<body>
    <div class="admin-sidebar">
        <div class="logo">OVA<span>RALL</span></div>
        <ul class="admin-nav">
            <li><a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
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
            <h2>Dashboard</h2>
            <div>
                <span class="text-muted">Welcome, <?php echo $_SESSION['user_name']; ?></span>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="stat-card d-flex justify-content-between align-items-center">
                    <div>
                        <h3><?php echo number_format($totalSales); ?></h3>
                        <p>Total Sales (BDT)</p>
                    </div>
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card d-flex justify-content-between align-items-center">
                    <div>
                        <h3><?php echo $totalOrders; ?></h3>
                        <p>Total Orders</p>
                    </div>
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card d-flex justify-content-between align-items-center">
                    <div>
                        <h3><?php echo $totalProducts; ?></h3>
                        <p>Products</p>
                    </div>
                    <i class="fas fa-box"></i>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card d-flex justify-content-between align-items-center">
                    <div>
                        <h3><?php echo $totalUsers; ?></h3>
                        <p>Customers</p>
                    </div>
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Recent Orders -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Orders</h5>
                        <a href="orders.php" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentOrders as $order): ?>
                                        <tr>
                                            <td><?php echo $order['order_number']; ?></td>
                                            <td><?php echo $order['user_name'] ?: 'Guest'; ?></td>
                                            <td><?php echo formatPrice($order['final_amount']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $order['status'] == 'delivered' ? 'success' : 
                                                         ($order['status'] == 'pending' ? 'warning' : 
                                                         ($order['status'] == 'processing' ? 'info' : 'secondary')); 
                                                ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Low Stock Alert -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Low Stock Alert</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($lowStock)): ?>
                            <p class="text-muted">All products have sufficient stock</p>
                        <?php else: ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($lowStock as $product): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?php echo $product['name']; ?>
                                        <span class="badge bg-danger"><?php echo $product['stock']; ?> left</span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <a href="products.php?action=add" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-plus"></i> Add Product
                        </a>
                        <a href="orders.php" class="btn btn-outline-primary w-100">
                            <i class="fas fa-list"></i> Manage Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
