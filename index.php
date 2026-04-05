<?php
$pageTitle = 'Home';
require_once 'includes/header.php';

// Get featured products
$db = getDB();
$featuredProducts = $db->query("SELECT p.*, c.name as category_name, c.slug as category_slug 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.is_featured = 1 AND p.is_active = 1 
    LIMIT 8")->fetchAll();

// Get new products
$newProducts = $db->query("SELECT p.*, c.name as category_name, c.slug as category_slug 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.is_new = 1 AND p.is_active = 1 
    LIMIT 8")->fetchAll();

// Get categories with product count
$categories = $db->query("SELECT c.*, COUNT(p.id) as product_count 
    FROM categories c 
    LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
    WHERE c.is_active = 1 AND c.parent_id IS NULL
    GROUP BY c.id
    ORDER BY c.sort_order")->fetchAll();
?>

<!-- Hero Section -->
<section class="hero-section">
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1920');">
                    <div class="container">
                        <div class="hero-content">
                            <span class="badge bg-primary mb-3">Summer Collection 2024</span>
                            <h1>Discover Your Style</h1>
                            <p>Premium fashion for the modern individual. Shop the latest trends.</p>
                            <div class="d-flex gap-3">
                                <a href="shop.php" class="btn btn-primary-custom">Shop Now</a>
                                <a href="shop.php?category=fashion" class="btn btn-outline-custom">Explore Collection</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=1920');">
                    <div class="container">
                        <div class="hero-content">
                            <span class="badge bg-success mb-3">New Arrivals</span>
                            <h1>Latest Electronics</h1>
                            <p>Upgrade your tech with the newest gadgets and devices.</p>
                            <div class="d-flex gap-3">
                                <a href="shop.php?category=electronics" class="btn btn-primary-custom">View Products</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=1920');">
                    <div class="container">
                        <div class="hero-content">
                            <span class="badge bg-info mb-3">Health & Wellness</span>
                            <h1>Stay Healthy</h1>
                            <p>Quality health products for you and your family.</p>
                            <div class="d-flex gap-3">
                                <a href="shop.php?category=health" class="btn btn-primary-custom">Shop Health</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h4>Free Shipping</h4>
                    <p class="text-muted">On all orders over ৳5,000</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-undo"></i>
                    </div>
                    <h4>Easy Returns</h4>
                    <p class="text-muted">30 days return policy</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4>Secure Payment</h4>
                    <p class="text-muted">100% secure checkout</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h4>24/7 Support</h4>
                    <p class="text-muted">Call: <?php echo CONTACT_PHONE; ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5">
    <div class="container">
        <div class="section-title">
            <span>Our Collection</span>
            <h2>Shop by Category</h2>
        </div>
        <div class="row">
            <?php foreach ($categories as $category): ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <a href="shop.php?category=<?php echo $category['slug']; ?>">
                        <div class="category-card">
                            <img src="<?php echo $category['image'] ?: 'https://via.placeholder.com/400x500?text=' . urlencode($category['name']); ?>" alt="<?php echo $category['name']; ?>">
                            <div class="category-overlay">
                                <h3><?php echo $category['name']; ?></h3>
                                <p><?php echo $category['product_count']; ?> Products</p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-title">
            <span>Featured Products</span>
            <h2>Trending Now</h2>
        </div>
        <div class="row">
            <?php foreach ($featuredProducts as $product): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-card">
                        <div class="product-image">
                            <a href="product.php?slug=<?php echo $product['slug']; ?>">
                                <img src="<?php echo $product['image'] ?: 'https://via.placeholder.com/300x400?text=' . urlencode($product['name']); ?>" alt="<?php echo $product['name']; ?>">
                            </a>
                            <div class="product-badges">
                                <?php if ($product['is_new']): ?>
                                    <span class="badge-new">NEW</span>
                                <?php endif; ?>
                                <?php if ($product['sale_price']): ?>
                                    <span class="badge-sale">SALE</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-actions">
                                <button class="add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>" title="Add to Cart">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                                <button class="wishlist-btn" data-product-id="<?php echo $product['id']; ?>" title="Add to Wishlist">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                        </div>
                        <div class="product-info">
                            <div class="product-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star<?php echo $i > $product['rating'] ? '-half-alt' : ''; ?>"></i>
                                <?php endfor; ?>
                                <span class="text-muted">(<?php echo $product['review_count']; ?>)</span>
                            </div>
                            <a href="product.php?slug=<?php echo $product['slug']; ?>">
                                <h3 class="product-title"><?php echo $product['name']; ?></h3>
                            </a>
                            <div class="product-price">
                                <span class="price-current"><?php echo formatPrice($product['sale_price'] ?: $product['price']); ?></span>
                                <?php if ($product['sale_price']): ?>
                                    <span class="price-old"><?php echo formatPrice($product['price']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="shop.php" class="btn btn-primary-custom">View All Products</a>
        </div>
    </div>
</section>

<!-- New Arrivals -->
<section class="py-5">
    <div class="container">
        <div class="section-title">
            <span>New Arrivals</span>
            <h2>Just Arrived</h2>
        </div>
        <div class="row">
            <?php foreach ($newProducts as $product): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-card">
                        <div class="product-image">
                            <a href="product.php?slug=<?php echo $product['slug']; ?>">
                                <img src="<?php echo $product['image'] ?: 'https://via.placeholder.com/300x400?text=' . urlencode($product['name']); ?>" alt="<?php echo $product['name']; ?>">
                            </a>
                            <div class="product-badges">
                                <span class="badge-new">NEW</span>
                                <?php if ($product['sale_price']): ?>
                                    <span class="badge-sale">SALE</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-actions">
                                <button class="add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>" title="Add to Cart">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                                <button class="wishlist-btn" data-product-id="<?php echo $product['id']; ?>" title="Add to Wishlist">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                        </div>
                        <div class="product-info">
                            <div class="product-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star<?php echo $i > $product['rating'] ? '-half-alt' : ''; ?>"></i>
                                <?php endfor; ?>
                                <span class="text-muted">(<?php echo $product['review_count']; ?>)</span>
                            </div>
                            <a href="product.php?slug=<?php echo $product['slug']; ?>">
                                <h3 class="product-title"><?php echo $product['name']; ?></h3>
                            </a>
                            <div class="product-price">
                                <span class="price-current"><?php echo formatPrice($product['sale_price'] ?: $product['price']); ?></span>
                                <?php if ($product['sale_price']): ?>
                                    <span class="price-old"><?php echo formatPrice($product['price']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Promo Banner -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="position-relative rounded overflow-hidden" style="height: 300px;">
                    <img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?w=800" class="w-100 h-100 object-fit-cover" alt="Fashion">
                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center" style="background: linear-gradient(to right, rgba(0,0,0,0.7), transparent);">
                        <div class="p-4 text-white">
                            <h3 class="text-white">Summer Sale</h3>
                            <p>Up to 50% off on fashion items</p>
                            <a href="shop.php?category=fashion" class="btn btn-primary-custom">Shop Now</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="position-relative rounded overflow-hidden" style="height: 300px;">
                    <img src="https://images.unsplash.com/photo-1550009158-9ebf69173e03?w=800" class="w-100 h-100 object-fit-cover" alt="Electronics">
                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center" style="background: linear-gradient(to right, rgba(0,0,0,0.7), transparent);">
                        <div class="p-4 text-white">
                            <h3 class="text-white">New Gadgets</h3>
                            <p>Latest electronics at best prices</p>
                            <a href="shop.php?category=electronics" class="btn btn-primary-custom">Explore</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact CTA -->
<section class="py-5 bg-dark text-white">
    <div class="container text-center">
        <h2 class="text-white mb-3">Need Help?</h2>
        <p class="mb-4">Our customer support team is available 24/7 to assist you</p>
        <div class="d-flex justify-content-center gap-4 flex-wrap">
            <a href="tel:<?php echo CONTACT_PHONE; ?>" class="btn btn-primary-custom">
                <i class="fas fa-phone me-2"></i> Call <?php echo CONTACT_PHONE; ?>
            </a>
            <a href="https://wa.me/<?php echo CONTACT_WHATSAPP; ?>" class="btn btn-success" target="_blank">
                <i class="fab fa-whatsapp me-2"></i> WhatsApp
            </a>
            <a href="mailto:<?php echo CONTACT_EMAIL; ?>" class="btn btn-outline-light">
                <i class="fas fa-envelope me-2"></i> Email Us
            </a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
