<?php
// Iphilo Fragrance Helper Functions
require_once __DIR__ . '/db.php';

/**
 * General Utilities
 */
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function format_currency($amount) {
    return 'R ' . number_format($amount, 2);
}

function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Site Settings Retrieval
 */
function get_site_setting($key) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    return $stmt->fetchColumn();
}

function get_all_social_links() {
    global $pdo;
    return $pdo->query("SELECT * FROM social_links")->fetchAll();
}

/**
 * Product & Category Utilities
 */
function get_all_categories() {
    global $pdo;
    return $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
}

function get_product_image($product_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT image_path FROM product_images WHERE product_id = ? AND is_primary = 1 LIMIT 1");
    $stmt->execute([$product_id]);
    $image = $stmt->fetchColumn();
    return $image ? "assets/uploads/products/$image" : "assets/images/no-image.jpg";
}

/**
 * Cart Utilities
 */
function get_cart_count() {
    if (isset($_SESSION['cart'])) {
        return array_sum(array_column($_SESSION['cart'], 'quantity'));
    }
    return 0;
}

function get_cart_total() {
    $total = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
    }
    return $total;
}

/**
 * Order Utilities
 */
function generate_order_number() {
    return 'IPH-' . strtoupper(bin2hex(random_bytes(4)));
}

/**
 * CRM Utilities
 */
function log_customer_activity($customer_id, $type, $description) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO customer_activity_logs (customer_id, activity_type, activity_description) VALUES (?, ?, ?)");
    $stmt->execute([$customer_id, $type, $description]);
}
?>
