<?php
$pageTitle = 'My Account';
require_once 'includes/config.php';

// Check if logged in
if (!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = 'dashboard.php';
    setFlash('error', 'Please login to view your account');
    redirect('login.php');
}

$db = getDB();

// Get user orders
$orders = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$orders->execute([$_SESSION['user_id']]);
$userOrders = $orders->fetchAll();

require_once 'includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-user-circle fa-4x text-primary"></i>
                        </div>
                        <h5><?php echo $_SESSION['user_name']; ?></h5>
                        <p class="text-muted"><?php echo $_SESSION['user_email']; ?></p>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="dashboard.php" class="list-group-item list-group-item-action active">
                            <i class="fas fa-shopping-bag me-2"></i> My Orders
                        </a>
                        <a href="wishlist.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-heart me-2"></i> Wishlist
                        </a>
                        <a href="profile.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-user me-2"></i> Profile
                        </a>
                        <a href="logout.php" class="list-group-item list-group-item-action text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-9">
                <h3 class="mb-4">My Orders</h3>
                
                <?php if (empty($userOrders)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                        <h5>No orders yet</h5>
                        <p class="text-muted">Start shopping to see your orders here</p>
                        <a href="shop.php" class="btn btn-primary-custom">Shop Now</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($userOrders as $order): ?>
                                    <tr>
                                        <td><?php echo $order['order_number']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
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
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#orderModal<?php echo $order['id']; ?>">
                                                View
                                            </button>
                                        </td>
                                    </tr>
                                    
                                    <!-- Order Details Modal -->
                                    <div class="modal fade" id="orderModal<?php echo $order['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Order <?php echo $order['order_number']; ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Shipping Address:</strong><br>
                                                    <?php echo $order['shipping_address']; ?>, <?php echo $order['shipping_city']; ?></p>
                                                    <p><strong>Payment Method:</strong> <?php echo strtoupper($order['payment_method']); ?></p>
                                                    <hr>
                                                    <p><strong>Subtotal:</strong> <?php echo formatPrice($order['total_amount']); ?></p>
                                                    <p><strong>Shipping:</strong> <?php echo formatPrice($order['shipping_amount']); ?></p>
                                                    <p><strong>Total:</strong> <?php echo formatPrice($order['final_amount']); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
