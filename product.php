<?php
require_once 'includes/config.php';

$slug = isset($_GET['slug']) ? $_GET['slug'] : null;

if (!$slug) {
    setFlash('error', 'Product not found');
    redirect('shop.php');
}

$db = getDB();
$stmt = $db->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.slug = ? AND p.is_active = 1");
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    setFlash('error', 'Product not found');
    redirect('shop.php');
}

$pageTitle = $product['name'];

// Get related products
$relatedStmt = $db->prepare("SELECT * FROM products 
    WHERE category_id = ? AND id != ? AND is_active = 1 
    LIMIT 4");
$relatedStmt->execute([$product['category_id'], $product['id']]);
$relatedProducts = $relatedStmt->fetchAll();

// Parse sizes and colors
$sizes = $product['sizes'] ? explode(',', $product['sizes']) : [];
$colors = $product['colors'] ? explode(',', $product['colors']) : [];

// Calculate discount
$discount = 0;
if ($product['sale_price'] && $product['price'] > 0) {
    $discount = round((($product['price'] - $product['sale_price']) / $product['price']) * 100);
}

require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<section class="py-4 bg-light">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="shop.php">Shop</a></li>
                <?php if ($product['category_slug']): ?>
                    <li class="breadcrumb-item"><a href="shop.php?category=<?php echo $product['category_slug']; ?>"><?php echo $product['category_name']; ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active"><?php echo $product['name']; ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Product Details -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Product Images -->
            <div class="col-lg-5 mb-4">
                <div class="card border-0 shadow-sm">
                    <img src="<?php echo $product['image'] ?: 'https://via.placeholder.com/500x600?text=' . urlencode($product['name']); ?>" 
                         class="card-img-top product-main-image" alt="<?php echo $product['name']; ?>">
                </div>
                <?php if ($product['gallery']): ?>
                    <div class="row mt-3">
                        <?php foreach (explode(',', $product['gallery']) as $img): ?>
                            <div class="col-3">
                                <img src="<?php echo trim($img); ?>" class="img-thumbnail product-thumbnail cursor-pointer" 
                                     data-image="<?php echo trim($img); ?>" alt="">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Product Info -->
            <div class="col-lg-7">
                <div class="ps-lg-4">
                    <!-- Badges -->
                    <div class="mb-3">
                        <?php if ($product['is_new']): ?>
                            <span class="badge bg-primary me-2">New</span>
                        <?php endif; ?>
                        <?php if ($discount > 0): ?>
                            <span class="badge bg-success">-<?php echo $discount; ?>%</span>
                        <?php endif; ?>
                    </div>
                    
                    <h1 class="mb-3"><?php echo $product['name']; ?></h1>
                    
                    <!-- Rating -->
                    <div class="mb-3">
                        <span class="text-warning">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star<?php echo $i > $product['rating'] ? '-half-alt' : ''; ?>"></i>
                            <?php endfor; ?>
                        </span>
                        <span class="text-muted ms-2">(<?php echo $product['review_count']; ?> reviews)</span>
                    </div>
                    
                    <!-- Price -->
                    <div class="mb-4">
                        <h2 class="text-primary mb-0">
                            <?php echo formatPrice($product['sale_price'] ?: $product['price']); ?>
                        </h2>
                        <?php if ($product['sale_price']): ?>
                            <span class="text-muted text-decoration-line-through">
                                <?php echo formatPrice($product['price']); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Short Description -->
                    <p class="text-muted mb-4"><?php echo $product['short_description'] ?: $product['description']; ?></p>
                    
                    <!-- Add to Cart Form -->
                    <form action="cart.php" method="POST" class="mb-4">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                        
                        <!-- Size Selection -->
                        <?php if (!empty($sizes)): ?>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Size:</label>
                                <div class="d-flex gap-2">
                                    <?php foreach ($sizes as $size): ?>
                                        <label class="btn btn-outline-secondary">
                                            <input type="radio" name="size" value="<?php echo trim($size); ?>" class="d-none" required>
                                            <?php echo trim($size); ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Color Selection -->
                        <?php if (!empty($colors)): ?>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Color:</label>
                                <div class="d-flex gap-2">
                                    <?php foreach ($colors as $color): ?>
                                        <label class="btn btn-outline-secondary">
                                            <input type="radio" name="color" value="<?php echo trim($color); ?>" class="d-none" required>
                                            <?php echo trim($color); ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Quantity -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Quantity:</label>
                            <div class="quantity-control" style="width: 150px;">
                                <button type="button" class="qty-minus">-</button>
                                <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" class="quantity-input">
                                <button type="button" class="qty-plus">+</button>
                            </div>
                            <small class="text-muted ms-3"><?php echo $product['stock']; ?> items available</small>
                        </div>
                        
                        <!-- Actions -->
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary-custom btn-lg">
                                <i class="fas fa-shopping-cart me-2"></i> Add to Cart
                            </button>
                            <a href="wishlist.php?action=add&id=<?php echo $product['id']; ?>" class="btn btn-outline-secondary btn-lg">
                                <i class="far fa-heart"></i>
                            </a>
                        </div>
                    </form>
                    
                    <!-- Features -->
                    <div class="row text-center border-top pt-4">
                        <div class="col-4">
                            <i class="fas fa-shipping-fast fa-2x text-primary mb-2"></i>
                            <p class="small mb-0">Free Shipping<br>over ৳5,000</p>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-undo fa-2x text-primary mb-2"></i>
                            <p class="small mb-0">Easy Returns<br>30 Days</p>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-shield-alt fa-2x text-primary mb-2"></i>
                            <p class="small mb-0">Secure<br>Payment</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Product Tabs -->
        <div class="row mt-5">
            <div class="col-12">
                <ul class="nav nav-tabs" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button">Description</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs" type="button">Specifications</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button">Reviews</button>
                    </li>
                </ul>
                <div class="tab-content p-4 border border-top-0 rounded-bottom" id="productTabsContent">
                    <div class="tab-pane fade show active" id="description">
                        <h4>Product Description</h4>
                        <p><?php echo nl2br($product['description']); ?></p>
                    </div>
                    <div class="tab-pane fade" id="specs">
                        <h4>Product Specifications</h4>
                        <table class="table table-bordered">
                            <tr>
                                <th>SKU</th>
                                <td><?php echo $product['sku'] ?: 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <th>Category</th>
                                <td><?php echo $product['category_name'] ?: 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <th>Stock</th>
                                <td><?php echo $product['stock']; ?> units</td>
                            </tr>
                            <?php if (!empty($sizes)): ?>
                                <tr>
                                    <th>Available Sizes</th>
                                    <td><?php echo $product['sizes']; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (!empty($colors)): ?>
                                <tr>
                                    <th>Available Colors</th>
                                    <td><?php echo $product['colors']; ?></td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="reviews">
                        <h4>Customer Reviews</h4>
                        <p class="text-muted">No reviews yet. Be the first to review this product!</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Related Products -->
        <?php if (!empty($relatedProducts)): ?>
            <div class="row mt-5">
                <div class="col-12">
                    <h3 class="mb-4">You May Also Like</h3>
                    <div class="row">
                        <?php foreach ($relatedProducts as $relProduct): ?>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="product-card">
                                    <div class="product-image">
                                        <a href="product.php?slug=<?php echo $relProduct['slug']; ?>">
                                            <img src="<?php echo $relProduct['image'] ?: 'https://via.placeholder.com/300x400?text=' . urlencode($relProduct['name']); ?>" alt="<?php echo $relProduct['name']; ?>">
                                        </a>
                                    </div>
                                    <div class="product-info">
                                        <a href="product.php?slug=<?php echo $relProduct['slug']; ?>">
                                            <h3 class="product-title"><?php echo $relProduct['name']; ?></h3>
                                        </a>
                                        <div class="product-price">
                                            <span class="price-current"><?php echo formatPrice($relProduct['sale_price'] ?: $relProduct['price']); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
