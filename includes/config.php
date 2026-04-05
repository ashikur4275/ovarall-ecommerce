<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ovarall_db');

// Site Configuration
define('SITE_NAME', 'OVARALL');
define('SITE_URL', 'http://localhost/ovarall-php');
define('CURRENCY', '৳');
define('CURRENCY_CODE', 'BDT');

// Contact Information
define('CONTACT_PHONE', '01981622758');
define('CONTACT_WHATSAPP', '01818622751');
define('CONTACT_EMAIL', 'ashikur4275@gmail.com');

// Session Start
session_start();

// Database Connection
function getDB() {
    static $db = null;
    if ($db === null) {
        try {
            $db = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    return $db;
}

// Helper Functions
function formatPrice($price) {
    return CURRENCY . number_format($price, 2);
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function getCartCount() {
    if (!isset($_SESSION['cart'])) {
        return 0;
    }
    return array_sum(array_column($_SESSION['cart'], 'quantity'));
}

function getCartTotal() {
    if (!isset($_SESSION['cart'])) {
        return 0;
    }
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

// Flash Messages
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>
