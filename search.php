<?php
$pageTitle = 'Search';
require_once 'includes/header.php';

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$products = [];

if ($search) {
    $db = getDB();
    $stmt = $db->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE (p.name LIKE ? OR p.description LIKE ?) AND p.is_active = 1");
    $stmt->execute(["%$search%", "%$search%"]);
    $products = $stmt->fetchAll();
}
?>

<section class="py-4 bg-light">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Search</li>
            </ol>
        </nav>
        <h1 class="mt-3">Search Results</h1>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <!-- Search Form -->
        <div class="row mb-5">
            <div class="col-md-6 mx-auto">
                <form action="search.php" method="GET">
                    <div class="input-group input-group-lg">
                        <input type="text" name="q" class="form-control" placeholder="Search products..." 
                               value="<?php echo htmlspecialchars($search); ?>" required>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <?php if ($search): ?>
            <p class="text-muted mb-4">Found <?php echo count($products); ?> results for "<?php echo htmlspecialchars($search); ?>"</p>
            
            <?php if (empty($products)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4>No products found</h4>
                    <p class="text-muted">Try different keywords or browse our categories</p>
                    <a href="shop.php" class="btn btn-primary-custom">Browse All Products</a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($products as $product): ?>
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
                                        <a href="wishlist.php?action=add&id=<?php echo $product['id']; ?>" title="Add to Wishlist">
                                            <i class="far fa-heart"></i>
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
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4>Enter a search term</h4>
                <p class="text-muted">Search for products by name or description</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
