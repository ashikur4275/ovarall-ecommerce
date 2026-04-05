<?php
$pageTitle = 'Wishlist';
require_once 'includes/config.php';

// Handle add/remove
$action = isset($_GET['action']) ? $_GET['action'] : '';
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($action == 'add' && $productId > 0) {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = 'wishlist.php';
        setFlash('error', 'Please login to add items to wishlist');
        redirect('login.php');
    }
    
    $db = getDB();
    try {
        $db->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)")
           ->execute([$_SESSION['user_id'], $productId]);
        setFlash('success', 'Added to wishlist');
    } catch (PDOException $e) {
        // Already in wishlist
    }
    redirect('wishlist.php');
}

if ($action == 'remove' && $productId > 0) {
    $db = getDB();
    $db->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?")
       ->execute([$_SESSION['user_id'], $productId]);
    setFlash('success', 'Removed from wishlist');
    redirect('wishlist.php');
}

require_once 'includes/header.php';

$wishlistItems = [];
if (isLoggedIn()) {
    $db = getDB();
    $stmt = $db->prepare("SELECT p.* FROM products p 
        INNER JOIN wishlist w ON p.id = w.product_id 
        WHERE w.user_id = ? AND p.is_active = 1");
    $stmt->execute([$_SESSION['user_id']]);
    $wishlistItems = $stmt->fetchAll();
}
?>

<section class="py-4 bg-light">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Wishlist</li>
            </ol>
        </nav>
        <h1 class="mt-3">My Wishlist</h1>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <?php if (!isLoggedIn()): ?>
            <div class="text-center py-5">
                <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                <h4>Please login to view your wishlist</h4>
                <p class="text-muted">Login to save your favorite items</p>
                <a href="login.php" class="btn btn-primary-custom">Login</a>
            </div>
        <?php elseif (empty($wishlistItems)): ?>
            <div class="text-center py-5">
                <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                <h4>Your wishlist is empty</h4>
                <p class="text-muted">Add items to your wishlist to save them for later</p>
                <a href="shop.php" class="btn btn-primary-custom">Browse Products</a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($wishlistItems as $product): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="product-card">
                            <div class="product-image">
                                <a href="product.php?slug=<?php echo $product['slug']; ?>">
                                    <img src="<?php echo $product['image'] ?: 'https://via.placeholder.com/300x400?text=' . urlencode($product['name']); ?>" alt="<?php echo $product['name']; ?>">
                                </a>
                                <div class="product-actions">
                                    <a href="cart.php?action=add&id=<?php echo $product['id']; ?>" title="Add to Cart">
                                        <i class="fas fa-shopping-cart"></i>
                                    </a>
                                    <a href="wishlist.php?action=remove&id=<?php echo $product['id']; ?>" title="Remove" class="text-danger">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="product-info">
                                <a href="product.php?slug=<?php echo $product['slug']; ?>">
                                    <h3 class="product-title"><?php echo $product['name']; ?></h3>
                                </a>
                                <div class="product-price">
                                    <span class="price-current"><?php echo formatPrice($product['sale_price'] ?: $product['price']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
