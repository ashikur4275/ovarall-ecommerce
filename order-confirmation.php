<?php
$pageTitle = 'Order Confirmation';
require_once 'includes/config.php';

$orderNumber = isset($_GET['order']) ? $_GET['order'] : null;

if (!$orderNumber) {
    redirect('index.php');
}

require_once 'includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="mb-4">
                    <i class="fas fa-check-circle fa-5x text-success"></i>
                </div>
                <h1 class="mb-3">Thank You for Your Order!</h1>
                <p class="lead text-muted mb-4">
                    Your order has been placed successfully. We'll send you a confirmation email shortly.
                </p>
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="mb-3">Order Details</h5>
                        <p class="mb-1"><strong>Order Number:</strong> <?php echo htmlspecialchars($orderNumber); ?></p>
                        <p class="mb-0 text-muted">
                            You can track your order status in your account dashboard.
                        </p>
                    </div>
                </div>
                
                <div class="d-flex justify-content-center gap-3">
                    <a href="shop.php" class="btn btn-primary-custom">
                        <i class="fas fa-shopping-bag me-2"></i> Continue Shopping
                    </a>
                    <?php if (isLoggedIn()): ?>
                        <a href="dashboard.php" class="btn btn-outline-secondary">
                            <i class="fas fa-user me-2"></i> My Account
                        </a>
                    <?php endif; ?>
                </div>
                
                <div class="mt-5">
                    <h5>Need Help?</h5>
                    <p class="text-muted">Contact us if you have any questions about your order</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="tel:<?php echo CONTACT_PHONE; ?>" class="btn btn-outline-primary">
                            <i class="fas fa-phone me-2"></i> Call <?php echo CONTACT_PHONE; ?>
                        </a>
                        <a href="https://wa.me/<?php echo CONTACT_WHATSAPP; ?>" class="btn btn-success" target="_blank">
                            <i class="fab fa-whatsapp me-2"></i> WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
