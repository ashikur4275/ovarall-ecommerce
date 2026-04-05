<?php
$pageTitle = 'Shopping Cart';
require_once 'includes/config.php';

// Handle cart actions
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

if ($action == 'add') {
    $productId = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);
    $quantity = isset($_GET['qty']) ? (int)$_GET['qty'] : (isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1);
    $size = isset($_POST['size']) ? $_POST['size'] : '';
    $color = isset($_POST['color']) ? $_POST['color'] : '';
    
    if ($productId > 0) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM products WHERE id = ? AND is_active = 1");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        
        if ($product) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            
            $cartKey = $productId . ($size ? '_' . $size : '') . ($color ? '_' . $color : '');
            
            if (isset($_SESSION['cart'][$cartKey])) {
                $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$cartKey] = [
                    'id' => $productId,
                    'name' => $product['name'],
                    'price' => $product['sale_price'] ?: $product['price'],
                    'original_price' => $product['price'],
                    'image' => $product['image'],
                    'quantity' => $quantity,
                    'size' => $size,
                    'color' => $color,
                    'stock' => $product['stock']
                ];
            }
            
            setFlash('success', $product['name'] . ' added to cart!');
        }
    }
    redirect('cart.php');
}

if ($action == 'update') {
    $cartKey = isset($_POST['key']) ? $_POST['key'] : '';
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    if ($cartKey && isset($_SESSION['cart'][$cartKey])) {
        if ($quantity > 0) {
            $_SESSION['cart'][$cartKey]['quantity'] = $quantity;
        } else {
            unset($_SESSION['cart'][$cartKey]);
        }
    }
    
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        echo json_encode(['success' => true, 'cart_count' => getCartCount(), 'cart_total' => getCartTotal()]);
        exit;
    }
    redirect('cart.php');
}

if ($action == 'remove') {
    $cartKey = isset($_GET['key']) ? $_GET['key'] : '';
    
    if ($cartKey && isset($_SESSION['cart'][$cartKey])) {
        unset($_SESSION['cart'][$cartKey]);
        setFlash('success', 'Item removed from cart');
    }
    
    redirect('cart.php');
}

if ($action == 'clear') {
    $_SESSION['cart'] = [];
    setFlash('success', 'Cart cleared');
    redirect('cart.php');
}

require_once 'includes/header.php';

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
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
                <li class="breadcrumb-item active">Shopping Cart</li>
            </ol>
        </nav>
        <h1 class="mt-3">Shopping Cart</h1>
    </div>
</section>

<!-- Cart Section -->
<section class="py-5">
    <div class="container">
        <?php if (empty($cart)): ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                <h3>Your cart is empty</h3>
                <p class="text-muted mb-4">Looks like you haven't added anything to your cart yet.</p>
                <a href="shop.php" class="btn btn-primary-custom btn-lg">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="row">
                <!-- Cart Items -->
                <div class="col-lg-8">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5><?php echo count($cart); ?> Item(s) in Cart</h5>
                        <a href="cart.php?action=clear" class="text-danger" onclick="return confirm('Are you sure you want to clear your cart?')">
                            <i class="fas fa-trash"></i> Clear Cart
                        </a>
                    </div>
                    
                    <?php foreach ($cart as $key => $item): ?>
                        <div class="cart-item">
                            <div class="cart-item-image">
                                <img src="<?php echo $item['image'] ?: 'https://via.placeholder.com/100x120?text=' . urlencode($item['name']); ?>" alt="<?php echo $item['name']; ?>">
                            </div>
                            <div class="cart-item-details">
                                <h4 class="cart-item-title">
                                    <a href="product.php?id=<?php echo $item['id']; ?>"><?php echo $item['name']; ?></a>
                                </h4>
                                <?php if ($item['size'] || $item['color']): ?>
                                    <p class="cart-item-variant">
                                        <?php if ($item['size']): ?>Size: <?php echo $item['size']; ?> | <?php endif; ?>
                                        <?php if ($item['color']): ?>Color: <?php echo $item['color']; endif; ?>
                                    </p>
                                <?php endif; ?>
                                <p class="cart-item-price"><?php echo formatPrice($item['price']); ?></p>
                            </div>
                            <div class="d-flex align-items-center gap-3">
    <div class="quantity-control">
        <button type="button" class="qty-minus" data-key="<?php echo $key; ?>">-</button>
        <input type="number" value="<?php echo $item['quantity']; ?>" min="0" max="<?php echo $item['stock']; ?>" class="quantity-input" data-key="<?php echo $key; ?>">
        <button type="button" class="qty-plus" data-key="<?php echo $key; ?>">+</button>
    </div>
    <div class="text-end">
        <p class="fw-bold mb-0"><?php echo formatPrice($item['price'] * $item['quantity']); ?></p>
        <a href="cart.php?action=remove&key=<?php echo $key; ?>" class="text-danger small" onclick="return confirm('Remove this item?')">
            <i class="fas fa-trash"></i> Remove
        </a>
    </div>
</div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="mt-4">
                        <a href="shop.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Continue Shopping
                        </a>
                    </div>
                </div>
                
                <!-- Cart Summary -->
                <div class="col-lg-4">
                    <div class="cart-summary">
                        <h4 class="mb-4">Order Summary</h4>
                        
                        <!-- Coupon -->
                        <div class="mb-4">
                            <label class="form-label">Have a coupon?</label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Enter code">
                                <button class="btn btn-outline-secondary" type="button">Apply</button>
                            </div>
                        </div>
                        
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span><?php echo formatPrice($subtotal); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping</span>
                            <span><?php echo $shipping == 0 ? 'Free' : formatPrice($shipping); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Tax</span>
                            <span>Calculated at checkout</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span><?php echo formatPrice($total); ?></span>
                        </div>
                        
                        <a href="checkout.php" class="btn btn-primary-custom w-100 btn-lg mt-4">
                            Proceed to Checkout
                        </a>
                        
                        <p class="text-muted small text-center mt-3">
                            <i class="fas fa-lock"></i> Secure checkout
                        </p>
                    </div>
                    
                    <!-- Contact Info -->
                    <div class="card mt-4 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="mb-3">Need Help?</h6>
                            <p class="small mb-2">
                                <i class="fas fa-phone text-primary me-2"></i>
                                <a href="tel:<?php echo CONTACT_PHONE; ?>"><?php echo CONTACT_PHONE; ?></a>
                            </p>
                            <p class="small mb-2">
                                <i class="fab fa-whatsapp text-success me-2"></i>
                                <a href="https://wa.me/<?php echo CONTACT_WHATSAPP; ?>" target="_blank"><?php echo CONTACT_WHATSAPP; ?></a>
                            </p>
                            <p class="small mb-0">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                <a href="mailto:<?php echo CONTACT_EMAIL; ?>"><?php echo CONTACT_EMAIL; ?></a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateQuantity(key, quantity) {
        if (quantity < 0) quantity = 0;
        
        fetch('cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'action=update&key=' + encodeURIComponent(key) + '&quantity=' + quantity
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
    
    document.querySelectorAll('.qty-minus').forEach(button => {
        button.addEventListener('click', function() {
            let key = this.dataset.key;
            let input = document.querySelector(`.quantity-input[data-key="${key}"]`);
            let currentValue = parseInt(input.value);
            if (currentValue > 0) {
                updateQuantity(key, currentValue - 1);
            }
        });
    });
    
    document.querySelectorAll('.qty-plus').forEach(button => {
        button.addEventListener('click', function() {
            let key = this.dataset.key;
            let input = document.querySelector(`.quantity-input[data-key="${key}"]`);
            let currentValue = parseInt(input.value);
            let max = parseInt(input.max);
            if (currentValue < max) {
                updateQuantity(key, currentValue + 1);
            }
        });
    });
    
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            let key = this.dataset.key;
            let newValue = parseInt(this.value);
            if (!isNaN(newValue)) {
                updateQuantity(key, newValue);
            }
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
