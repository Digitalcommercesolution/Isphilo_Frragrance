<?php
// Iphilo Fragrance Cart API
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$product_id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
$quantity = isset($_REQUEST['quantity']) ? (int)$_REQUEST['quantity'] : 1;

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Apply action
$success = false;
$message = '';

switch ($action) {
    case 'add':
        if ($product_id > 0) {
            $stmt = $pdo->prepare("SELECT p.id, p.name, p.price, p.sale_price, c.name as category FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
            
            if ($product) {
                $price = $product['sale_price'] ? $product['sale_price'] : $product['price'];
                
                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id]['quantity'] += $quantity;
                } else {
                    $_SESSION['cart'][$product_id] = [
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'price' => (float)$price,
                        'category' => $product['category'],
                        'quantity' => $quantity
                    ];
                }
                $success = true;
                $message = $product['name'] . ' added to cart!';
            } else {
                $message = 'Product not found.';
            }
        }
        break;
        
    case 'update':
        if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
                $success = true;
                $message = 'Cart updated.';
            } else {
                unset($_SESSION['cart'][$product_id]);
                $success = true;
                $message = 'Item removed from cart.';
            }
        }
        break;
        
    case 'remove':
        if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            $success = true;
            $message = 'Item removed from cart.';
        }
        break;
        
    case 'clear':
        $_SESSION['cart'] = [];
        $success = true;
        $message = 'Cart cleared.';
        break;
        
    case 'get_count':
        $count = 0;
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $count += $item['quantity'];
            }
        }
        echo json_encode(['success' => true, 'count' => $count]);
        exit;
        
    case 'apply_coupon':
        $coupon_code = isset($_POST['coupon_code']) ? sanitize($_POST['coupon_code']) : '';
        // Placeholder for coupon logic
        $message = 'Coupons are currently disabled.';
        break;
}

// Check if AJAX request
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    $count = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['quantity'];
        }
    }
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'cart_count' => $count
    ]);
    exit;
}

// Redirect back to previous page or cart
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../cart.php';
redirect($referer);
?>
