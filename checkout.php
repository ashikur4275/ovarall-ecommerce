<?php
$pageTitle = 'Checkout';
require_once 'includes/config.php';

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    setFlash('error', 'Your cart is empty');
    redirect('shop.php');
}

// Handle checkout submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = getDB();
    
    // Generate order number
    $orderNumber = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);
    
    // Calculate totals
    $subtotal = getCartTotal();
    $shipping = $subtotal > 5000 ? 0 : 150;
    $total = $subtotal + $shipping;
    
    // Get user ID if logged in
    $userId = isLoggedIn() ? $_SESSION['user_id'] : null;
    
    // Insert order
    $stmt = $db->prepare("INSERT INTO orders 
        (order_number, user_id, total_amount, shipping_amount, final_amount, status, 
         payment_method, shipping_name, shipping_email, shipping_phone, shipping_address, shipping_city) 
        VALUES (?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $orderNumber,
        $userId,
        $subtotal,
        $shipping,
        $total,
        $_POST['payment_method'],
        $_POST['full_name'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['address'],
        $_POST['city']
    ]);
    
    $orderId = $db->lastInsertId();
    
    // Insert order items
    $itemStmt = $db->prepare("INSERT INTO order_items 
        (order_id, product_id, product_name, product_image, price, quantity, size, color, total) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($_SESSION['cart'] as $item) {
        $itemStmt->execute([
            $orderId,
            $item['id'],
            $item['name'],
            $item['image'],
            $item['price'],
            $item['quantity'],
            $item['size'],
            $item['color'],
            $item['price'] * $item['quantity']
        ]);
        
        // Update stock
        $db->prepare("UPDATE products SET stock = stock - ? WHERE id = ?")
           ->execute([$item['quantity'], $item['id']]);
    }
    
    // Clear cart
    $_SESSION['cart'] = [];
    
    setFlash('success', 'Order placed successfully! Your order number is: ' . $orderNumber);
    redirect('order-confirmation.php?order=' . $orderNumber);
}

require_once 'includes/header.php';

$subtotal = getCartTotal();
$shipping = $subtotal > 5000 ? 0 : 150;
$total = $subtotal + $shipping;
?>

<!-- Page Header -->
<section class="py-4 bg-light">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="cart.php">Cart</a></li>
                <li class="breadcrumb-item active">Checkout</li>
            </ol>
        </nav>
        <h1 class="mt-3">Checkout</h1>
    </div>
</section>

<!-- Checkout Section -->
<section class="py-5">
    <div class="container">
        <form action="checkout.php" method="POST" data-validate>
            <div class="row">
                <!-- Shipping Info -->
                <div class="col-lg-8">
                    <div class="checkout-section">
                        <h4 class="checkout-title">Shipping Information</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="full_name" class="form-control" required 
                                       value="<?php echo isLoggedIn() ? $_SESSION['user_name'] : ''; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control" required
                                       value="<?php echo isLoggedIn() ? $_SESSION['user_email'] : ''; ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone *</label>
                                <input type="tel" name="phone" class="form-control" required
                                       value="<?php echo isLoggedIn() ? ($_SESSION['user_phone'] ?? '') : ''; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City *</label>
                                <input type="text" name="city" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address *</label>
                            <textarea name="address" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Order Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Special instructions for delivery"></textarea>
                        </div>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="checkout-section">
                        <h4 class="checkout-title">Payment Method</h4>
                        
                        <div class="payment-method active" onclick="selectPayment('cod')">
                            <div class="d-flex align-items-center">
                                <input type="radio" name="payment_method" value="cod" checked class="me-3">
                                <div>
                                    <h6 class="mb-1">Cash on Delivery</h6>
                                    <p class="mb-0 text-muted small">Pay when you receive your order</p>
                                </div>
                                <i class="fas fa-money-bill-wave ms-auto fa-2x text-success"></i>
                            </div>
                        </div>
                        
                        <div class="payment-method" onclick="selectPayment('bkash')">
                            <div class="d-flex align-items-center">
                                <input type="radio" name="payment_method" value="bkash" class="me-3">
                                <div>
                                    <h6 class="mb-1">bKash</h6>
                                    <p class="mb-0 text-muted small">Pay with bKash</p>
                                </div>
                                <span class="ms-auto fw-bold text-danger">bKash</span>
                            </div>
                        </div>
                        
                        <div class="payment-method" onclick="selectPayment('nagad')">
                            <div class="d-flex align-items-center">
                                <input type="radio" name="payment_method" value="nagad" class="me-3">
                                <div>
                                    <h6 class="mb-1">Nagad</h6>
                                    <p class="mb-0 text-muted small">Pay with Nagad</p>
                                </div>
                                <span class="ms-auto fw-bold text-warning">Nagad</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="cart-summary">
                        <h4 class="mb-4">Order Summary</h4>
                        
                        <!-- Cart Items -->
                        <div class="mb-4" style="max-height: 300px; overflow-y: auto;">
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                                <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                                    <img src="<?php echo $item['image'] ?: 'https://via.placeholder.com/60x80'; ?>" 
                                         alt="<?php echo $item['name']; ?>" style="width: 60px; height: 80px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?php echo $item['name']; ?></h6>
                                        <?php if ($item['size'] || $item['color']): ?>
                                            <p class="small text-muted mb-1">
                                                <?php if ($item['size']) echo 'Size: ' . $item['size']; ?>
                                                <?php if ($item['color']) echo ' Color: ' . $item['color']; ?>
                                            </p>
                                        <?php endif; ?>
                                        <p class="mb-0"><?php echo $item['quantity']; ?> x <?php echo formatPrice($item['price']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span><?php echo formatPrice($subtotal); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping</span>
                            <span><?php echo $shipping == 0 ? 'Free' : formatPrice($shipping); ?></span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span><?php echo formatPrice($total); ?></span>
                        </div>
                        
                        <button type="submit" class="btn btn-primary-custom w-100 btn-lg mt-4">
                            Place Order
                        </button>
                        
                        <p class="text-muted small text-center mt-3">
                            <i class="fas fa-lock"></i> Your information is secure
                        </p>
                    </div>
                    
                    <!-- Contact -->
                    <div class="card mt-4 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h6>Need Help?</h6>
                            <a href="tel:<?php echo CONTACT_PHONE; ?>" class="btn btn-outline-primary btn-sm w-100 mb-2">
                                <i class="fas fa-phone"></i> Call <?php echo CONTACT_PHONE; ?>
                            </a>
                            <a href="https://wa.me/<?php echo CONTACT_WHATSAPP; ?>" class="btn btn-success btn-sm w-100" target="_blank">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
function selectPayment(method) {
    document.querySelectorAll('.payment-method').forEach(el => el.classList.remove('active'));
    event.currentTarget.classList.add('active');
    document.querySelector('input[name="payment_method"][value="' + method + '"]').checked = true;
}
</script>

<?php require_once 'includes/footer.php'; ?>
