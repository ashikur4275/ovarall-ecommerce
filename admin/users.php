<?php
require_once '../includes/config.php';

if (!isAdmin()) {
    setFlash('error', 'Access denied');
    redirect('../index.php');
}

$db = getDB();

// Get all users
$users = $db->query("SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - OVARALL Admin</title>
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
    </style>
</head>
<body>
    <div class="admin-sidebar">
        <div class="logo">OVA<span>RALL</span></div>
        <ul class="admin-nav">
            <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="users.php" class="active"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="categories.php"><i class="fas fa-tags"></i> Categories</a></li>
            <li><hr class="border-secondary"></li>
            <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Store</a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <div class="admin-content">
        <h2 class="mb-4">Users</h2>
        
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo $user['name']; ?></td>
                                    <td><?php echo $user['email']; ?></td>
                                    <td><?php echo $user['phone'] ?: 'N/A'; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <a href="mailto:<?php echo $user['email']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-envelope"></i>
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
