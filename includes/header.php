<?php
require_once 'config.php';

// Get all categories for navigation
function getCategories($parent_id = null) {
    $db = getDB();
    $sql = "SELECT * FROM categories WHERE is_active = 1 AND parent_id " . 
           ($parent_id === null ? "IS NULL" : "= :parent_id") . 
           " ORDER BY sort_order";
    $stmt = $db->prepare($sql);
    if ($parent_id !== null) {
        $stmt->execute([':parent_id' => $parent_id]);
    } else {
        $stmt->execute();
    }
    return $stmt->fetchAll();
}

// Get category by slug
function getCategoryBySlug($slug) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM categories WHERE slug = ? AND is_active = 1");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

$mainCategories = getCategories();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
        <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Custom Subcategory Styles -->
    <style>
        .sub-category-list {
            list-style: none;
            padding-left: 20px;
            margin-top: 5px;
        }
        
        .sub-category-list li a {
            font-size: 14px;
            color: #666;
            transition: all 0.3s;
            display: block;
            padding: 5px 0;
        }

        .sub-category-list li a:hover,
        .sub-category-list li a.active {
            color: #ff6f42;
            padding-left: 5px;
        }

        .sub-category-list .count {
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
    
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <a href="tel:<?php echo CONTACT_PHONE; ?>"><i class="fas fa-phone"></i> <?php echo CONTACT_PHONE; ?></a>
                    <a href="mailto:<?php echo CONTACT_EMAIL; ?>"><i class="fas fa-envelope"></i> <?php echo CONTACT_EMAIL; ?></a>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="https://wa.me/<?php echo CONTACT_WHATSAPP; ?>" target="_blank"><i class="fab fa-whatsapp"></i> <?php echo CONTACT_WHATSAPP; ?></a>
                    <?php if (isLoggedIn()): ?>
                        <a href="dashboard.php"><i class="fas fa-user"></i> My Account</a>
                    <?php else: ?>
                        <a href="login.php"><i class="fas fa-user"></i> Login / Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <!-- Logo -->
                <a class="logo" href="index.php">OVA<span>RALL</span></a>
                
                <!-- Mobile Toggle -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <!-- Navigation -->
                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $currentPage == 'index' ? 'active' : ''; ?>" href="index.php">HOME</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo $currentPage == 'shop' ? 'active' : ''; ?>" href="shop.php" id="shopDropdown" role="button" data-bs-toggle="dropdown">
                                SHOP
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="shop.php">All Products</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <?php foreach ($mainCategories as $cat): ?>
                                    <li>
                                        <a class="dropdown-item" href="shop.php?category=<?php echo $cat['slug']; ?>">
                                            <?php echo $cat['name']; ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo isset($_GET['category']) && $_GET['category'] == 'fashion' ? 'active' : ''; ?>" href="shop.php?category=fashion">FASHION</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo isset($_GET['category']) && $_GET['category'] == 'accessories' ? 'active' : ''; ?>" href="shop.php?category=accessories">ACCESSORIES</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo isset($_GET['category']) && in_array($_GET['category'], ['electronics', 'mobile', 'computer', 'earphone']) ? 'active' : ''; ?>" href="#" id="electronicsDropdown" role="button" data-bs-toggle="dropdown">
                                ELECTRONICS
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="shop.php?category=electronics">All Electronics</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="shop.php?category=mobile">Mobile</a></li>
                                <li><a class="dropdown-item" href="shop.php?category=computer">Computer</a></li>
                                <li><a class="dropdown-item" href="shop.php?category=earphone">Earphone</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo isset($_GET['category']) && $_GET['category'] == 'health' ? 'active' : ''; ?>" href="shop.php?category=health">HEALTH</a>
                        </li>
                    </ul>
                    
                    <!-- Header Actions -->
                    <div class="header-actions">
                        <a href="search.php" title="Search"><i class="fas fa-search"></i></a>
                        <a href="wishlist.php" title="Wishlist"><i class="fas fa-heart"></i></a>
                        <a href="cart.php" title="Cart">
                            <i class="fas fa-shopping-cart"></i>
                            <?php if (getCartCount() > 0): ?>
                                <span class="cart-count"><?php echo getCartCount(); ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Flash Messages -->
    <?php $flash = getFlash(); if ($flash): ?>
        <div class="container mt-3">
            <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show">
                <?php echo $flash['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>
