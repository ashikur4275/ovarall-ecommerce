<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

$action = isset($_POST['action']) ? $_POST['action'] : '';
$response = ['success' => false, 'message' => 'Invalid action'];

switch ($action) {
    case 'add':
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
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
                        'image' => $product['image'],
                        'quantity' => $quantity,
                        'size' => $size,
                        'color' => $color
                    ];
                }
                
                $response = [
                    'success' => true,
                    'message' => 'Added to cart',
                    'cart_count' => getCartCount(),
                    'cart_total' => getCartTotal()
                ];
            }
        }
        break;
        
    case 'update':
        $cartKey = isset($_POST['key']) ? $_POST['key'] : '';
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        
        if ($cartKey && isset($_SESSION['cart'][$cartKey])) {
            if ($quantity > 0) {
                $_SESSION['cart'][$cartKey]['quantity'] = $quantity;
            } else {
                unset($_SESSION['cart'][$cartKey]);
            }
            
            $response = [
                'success' => true,
                'cart_count' => getCartCount(),
                'cart_total' => getCartTotal()
            ];
        }
        break;
        
    case 'remove':
        $cartKey = isset($_POST['key']) ? $_POST['key'] : '';
        
        if ($cartKey && isset($_SESSION['cart'][$cartKey])) {
            unset($_SESSION['cart'][$cartKey]);
            $response = [
                'success' => true,
                'cart_count' => getCartCount(),
                'cart_total' => getCartTotal()
            ];
        }
        break;
        
    case 'get':
        $response = [
            'success' => true,
            'cart' => $_SESSION['cart'] ?? [],
            'cart_count' => getCartCount(),
            'cart_total' => getCartTotal()
        ];
        break;
}

echo json_encode($response);
