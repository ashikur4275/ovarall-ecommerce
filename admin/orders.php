<?php
require_once '../includes/config.php';

if (!isAdmin()) {
    setFlash('error', 'Access denied');
    redirect('../index.php');
}

$db = getDB();

// Handle status update
if (isset($_POST['update_status'])) {
    $orderId = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $db->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$status, $orderId]);
    setFlash('success', 'Order status updated');
    redirect('orders.php');
}

// Get all orders
$orders = $db->query("SELECT o.*, u.name as user_name, u.email as user_email 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - OVARALL Admin</title>
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
            <li><a href="orders.php" class="active"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="categories.php"><i class="fas fa-tags"></i> Categories</a></li>
            <li><hr class="border-secondary"></li>
            <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Store</a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <div class="admin-content">
        <h2 class="mb-4">Orders</h2>
        
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?php echo $order['order_number']; ?></td>
                                    <td>
                                        <?php echo $order['user_name'] ?: 'Guest'; ?>
                                        <br><small class="text-muted"><?php echo $order['shipping_phone']; ?></small>
                                    </td>
                                    <td><?php echo formatPrice($order['final_amount']); ?></td>
                                    <td>
                                        <span class="badge bg-info"><?php echo strtoupper($order['payment_method']); ?></span>
                                    </td>
                                    <td>
                                        <form method="POST" class="d-flex gap-2">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <select name="status" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                                <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                    </td>
                                    <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#orderModal<?php echo $order['id']; ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                
                                <!-- Order Details Modal -->
                                <div class="modal fade" id="orderModal<?php echo $order['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Order <?php echo $order['order_number']; ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6>Shipping Information</h6>
                                                        <p>
                                                            <strong><?php echo $order['shipping_name']; ?></strong><br>
                                                            <?php echo $order['shipping_phone']; ?><br>
                                                            <?php echo $order['shipping_email']; ?><br>
                                                            <?php echo $order['shipping_address']; ?>, <?php echo $order['shipping_city']; ?>
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6>Order Summary</h6>
                                                        <p>
                                                            Subtotal: <?php echo formatPrice($order['total_amount']); ?><br>
                                                            Shipping: <?php echo formatPrice($order['shipping_amount']); ?><br>
                                                            <strong>Total: <?php echo formatPrice($order['final_amount']); ?></strong>
                                                        </p>
                                                    </div>
                                                </div>
                                                <hr>
                                                <h6>Order Items</h6>
                                                <?php
                                                $items = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
                                                $items->execute([$order['id']]);
                                                $orderItems = $items->fetchAll();
                                                ?>
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Product</th>
                                                            <th>Qty</th>
                                                            <th>Price</th>
                                                            <th>Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($orderItems as $item): ?>
                                                            <tr>
                                                                <td><?php echo $item['product_name']; ?></td>
                                                                <td><?php echo $item['quantity']; ?></td>
                                                                <td><?php echo formatPrice($item['price']); ?></td>
                                                                <td><?php echo formatPrice($item['total']); ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
