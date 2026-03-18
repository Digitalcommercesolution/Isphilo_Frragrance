<?php
// Iphilo Fragrance Checkout Processing API
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../checkout.php');
}

// 1. Get and Sanitize Form Data
$first_name = sanitize($_POST['first_name']);
$last_name = sanitize($_POST['last_name']);
$email = sanitize($_POST['email']);
$phone = sanitize($_POST['phone']);
$address_line1 = sanitize($_POST['address_line1']);
$address_line2 = sanitize($_POST['address_line2']);
$city = sanitize($_POST['city']);
$state = sanitize($_POST['state']);
$postal_code = sanitize($_POST['postal_code']);
$payment_method = sanitize($_POST['payment_method']);

// 2. Validate Cart
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    redirect('../cart.php');
}

$cart_items = $_SESSION['cart'];
$subtotal = get_cart_total();
$shipping = 0; // Shipping to be calculated
$total = $subtotal + $shipping;

// 3. Start Database Transaction
try {
    $pdo->beginTransaction();
    
    // a. Create Order Record
    $order_number = generate_order_number();
    $customer_id = is_customer_logged_in() ? $_SESSION['customer_id'] : NULL;
    $shipping_address = "$address_line1, $address_line2, $city, $state, $postal_code";
    
    $stmt = $pdo->prepare("INSERT INTO orders (order_number, customer_id, guest_email, subtotal, total, status, payment_method, shipping_address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $order_number, 
        $customer_id, 
        $email, 
        $subtotal, 
        $total, 
        'pending', 
        $payment_method, 
        $shipping_address
    ]);
    $order_id = $pdo->lastInsertId();
    
    // b. Create Order Items
    $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt_stock = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
    
    foreach ($cart_items as $id => $item) {
        $stmt_item->execute([$order_id, $id, $item['quantity'], $item['price']]);
        // Update stock (optional, can wait for payment)
        // $stmt_stock->execute([$item['quantity'], $id]);
    }
    
    // c. Handle Payment Proof Upload (if EFT)
    if ($payment_method === 'eft' && isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === 0) {
        $file_name = $order_number . '_' . time() . '_' . $_FILES['payment_proof']['name'];
        $upload_path = '../assets/uploads/payments/' . $file_name;
        
        if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $upload_path)) {
            $stmt_proof = $pdo->prepare("INSERT INTO payment_proofs (order_id, file_path) VALUES (?, ?)");
            $stmt_proof->execute([$order_id, $file_name]);
        }
    }
    
    // d. Log Activity (if customer is logged in)
    if ($customer_id) {
        log_customer_activity($customer_id, 'order_placed', "Placed order #$order_number");
    }
    
    $pdo->commit();
    
    // 4. Clear Cart and Redirect to Success Page
    $_SESSION['cart'] = [];
    $_SESSION['last_order_number'] = $order_number;
    $_SESSION['last_order_total'] = $total;
    
    redirect('../order-success.php');
    
} catch (Exception $e) {
    $pdo->rollBack();
    die("Error processing order: " . $e->getMessage());
}
?>
