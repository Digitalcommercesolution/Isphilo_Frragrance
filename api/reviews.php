<?php
// Iphilo Fragrance Reviews Processing API
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_customer_logged_in()) {
    redirect('../login.php');
}

$customer_id = $_SESSION['customer_id'];
$product_id = (int)$_POST['product_id'];
$rating = (int)$_POST['rating'];
$comment = sanitize($_POST['comment']);

// Validate
if ($rating < 1 || $rating > 5 || empty($comment)) {
    redirect("../product.php?id=$product_id&error=invalid_review");
}

// Insert Review (Pending Approval)
$stmt = $pdo->prepare("INSERT INTO product_reviews (product_id, customer_id, rating, comment, status) VALUES (?, ?, ?, ?, 'pending')");
if ($stmt->execute([$product_id, $customer_id, $rating, $comment])) {
    log_customer_activity($customer_id, 'review_submitted', "Submitted a review for product ID: $product_id");
    redirect("../product.php?id=$product_id&success=review_pending");
} else {
    redirect("../product.php?id=$product_id&error=review_failed");
}
?>
