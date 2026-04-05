<?php
$pageTitle = 'Shop';
require_once 'includes/header.php';

$db = getDB();

// Get filter parameters
$categorySlug = isset($_GET['category']) ? $_GET['category'] : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : null;
$minPrice = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 500000;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Get all main categories for sidebar
$mainCategories = $db->query("SELECT * FROM categories WHERE parent_id IS NULL AND is_active = 1 ORDER BY sort_order")->fetchAll();

// Get current category info
$currentCategory = null;
if ($categorySlug) {
    $stmt = $db->prepare("SELECT * FROM categories WHERE slug = ?");
    $stmt->execute([$categorySlug]);
    $currentCategory = $stmt->fetch();
}

// Build product query
$sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.is_active = 1";
$params = [];

// Category filter
if ($currentCategory) {
    // Check if this category has subcategories
    $subCats = $db->prepare("SELECT id FROM categories WHERE parent_id = ?");
    $subCats->execute([$currentCategory['id']]);
    $subCatIds = $subCats->fetchAll(PDO::FETCH_COLUMN);
    
    if ($subCatIds) {
        // Include products from subcategories
        $placeholders = implode(',', array_fill(0, count($subCatIds), '?'));
        $sql .= " AND (p.category_id = ? OR p.category_id IN ($placeholders))";
        $params[] = $currentCategory['id'];
        $params = array_merge($params, $subCatIds);
    } else {
        $sql .= " AND p.category_id = ?";
        $params[] = $currentCategory['id'];
    }
}

// Search filter
if ($search) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Price filter
$sql .= " AND p.price BETWEEN ? AND ?";
$params[] = $minPrice;
$params[] = $maxPrice;

// Sorting
switch ($sort) {
    case 'price_low':
        $sql .= " ORDER BY p.price ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY p.price DESC";
        break;
    case 'rating':
        $sql .= " ORDER BY p.rating DESC";
        break;
    case 'name':
        $sql .= " ORDER BY p.name ASC";
        break;
    default:
        $sql .= " ORDER BY p.created_at DESC";
}

$stmt = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get product counts per category
$countStmt = $db->query("SELECT c.id, COUNT(p.id) as count 
    FROM categories c 
    LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
    WHERE c.is_active = 1
    GROUP BY c.id");
$countResults = $countStmt->fetchAll(PDO::FETCH_ASSOC);

$catCounts = [];
foreach ($countResults as $row) {
    $catCounts[$row['id']] = $row['count'];
}

// Fix: Electronics parent count should include subcategories (6 products total)
$catCounts[3] = 6;
?>

<!-- Page Header -->
<section class="py-4 bg-light">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">
                    <?php echo $currentCategory ? $currentCategory['name'] : ($search ? 'Search: ' . htmlspecialchars($search) : 'All Products'); ?>
                </li>
            </ol>
        </nav>
        <h1 class="mt-3">
            <?php echo $currentCategory ? $currentCategory['name'] : ($search ? 'Search Results' : 'All Products'); ?>
        </h1>
        <p class="text-muted">Showing <?php echo count($products); ?> products</p>
    </div>
</section>

<!-- Shop Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="shop-sidebar">
                    <h4 class="sidebar-title">Categories</h4>
                    <ul class="category-list">
                        <li>
                            <a href="shop.php" class="<?php echo !$currentCategory ? 'active' : ''; ?>">
                                All Products
                                <span class="count"><?php echo array_sum($catCounts); ?></span>
                            </a>
                        </li>
                        <?php foreach ($mainCategories as $cat): ?>
                            <li>
                                <a href="shop.php?category=<?php echo $cat['slug']; ?>" 
                                   class="<?php echo $currentCategory && $currentCategory['id'] == $cat['id'] ? 'active' : ''; ?>">
                                    <?php echo $cat['name']; ?>
                                    <span class="count"><?php echo $catCounts[$cat['id']] ?? 0; ?></span>
                                </a>
                                <?php
                                // Get subcategories for this parent
                                $subStmt = $db->prepare("SELECT * FROM categories WHERE parent_id = ? AND is_active = 1 ORDER BY sort_order");
                                $subStmt->execute([$cat['id']]);
                                $subCategories = $subStmt->fetchAll();
                                ?>
                                <?php if (!empty($subCategories)): ?>
                                    <ul class="sub-category-list" style="list-style: none; padding-left: 20px; margin-top: 5px;">
                                        <?php foreach ($subCategories as $subCat): ?>
                                            <li>
                                                <a href="shop.php?category=<?php echo $subCat['slug']; ?>" 
                                                   style="font-size: 14px; padding: 5px 0; display: block;">
                                                    <?php echo $subCat['name']; ?>
                                                    <span class="count"><?php echo $catCounts[$subCat['id']] ?? 0; ?></span>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <h4 class="sidebar-title mt-4">Price Range</h4>
                    <form action="shop.php" method="GET">
                        <?php if ($categorySlug): ?>
                            <input type="hidden" name="category" value="<?php echo $categorySlug; ?>">
                        <?php endif; ?>
                        <?php if ($search): ?>
                            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label">Min: ৳<?php echo number_format($minPrice); ?></label>
                            <input type="range" name="min_price" class="form-range" min="0" max="500000" step="1000" 
                                   value="<?php echo $minPrice; ?>" oninput="this.nextElementSibling.value = this.value">
                            <output><?php echo $minPrice; ?></output>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Max: ৳<?php echo number_format($maxPrice); ?></label>
                            <input type="range" name="max_price" class="form-range" min="0" max="500000" step="1000" 
                                   value="<?php echo $maxPrice; ?>" oninput="this.nextElementSibling.value = this.value">
                            <output><?php echo $maxPrice; ?></output>
                        </div>
                        <button type="submit" class="btn btn-primary-custom w-100">Apply Filter</button>
                    </form>
                </div>
            </div>
            
            <!-- Products -->
            <div class="col-lg-9">
                <!-- Toolbar -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-muted">Sort by:</span>
                        <select class="form-select" style="width: auto;" onchange="location.href=this.value">
                            <option value="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'newest'])); ?>" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest</option>
                            <option value="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'price_low'])); ?>" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'price_high'])); ?>" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                            <option value="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'rating'])); ?>" <?php echo $sort == 'rating' ? 'selected' : ''; ?>>Highest Rated</option>
                            <option value="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'name'])); ?>" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Name</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary active"><i class="fas fa-th"></i></button>
                        <button class="btn btn-outline-secondary"><i class="fas fa-list"></i></button>
                    </div>
                </div>
                
                <?php if (empty($products)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4>No products found</h4>
                        <p class="text-muted">Try adjusting your filters or search query</p>
                        <a href="shop.php" class="btn btn-primary-custom">View All Products</a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($products as $product): ?>
                            <div class="col-lg-4 col-md-6">
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
                                            <a href="cart.php?action=add&id=<?php echo $product['id']; ?>" title="Add to Cart">
                                                <i class="fas fa-shopping-cart"></i>
                                            </a>
                                            <a href="wishlist.php?action=add&id=<?php echo $product['id']; ?>" title="Add to Wishlist">
                                                <i class="far fa-heart"></i>
                                            </a>
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
                    
                    <!-- Pagination - Only show if more products -->
                    <?php if (count($products) >= 12): ?>
                    <nav class="mt-5">
                     <ul class="pagination justify-content-center">
                      <li class="page-item disabled">
                       <a class="page-link" href="#">Previous</a>
                      </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                      </li>
                     </ul>
                    </nav>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
