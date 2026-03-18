<?php
// Iphilo Fragrance Search API
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

$query = isset($_GET['q']) ? sanitize($_GET['q']) : '';

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

try {
    // Search products by name, description, or category
    $stmt = $pdo->prepare("
        SELECT p.id, p.name, p.slug, p.price, p.sale_price, c.name as category_name 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE (p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?) 
        AND p.is_active = 1 
        LIMIT 10
    ");
    
    $searchTerm = "%$query%";
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    $results = $stmt->fetchAll();
    
    $formattedResults = [];
    foreach ($results as $product) {
        $formattedResults[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'slug' => $product['slug'],
            'category' => $product['category_name'],
            'price' => (float)$product['price'],
            'sale_price' => $product['sale_price'] ? (float)$product['sale_price'] : null,
            'image' => get_product_image($product['id']),
            'url' => 'product.php?slug=' . $product['slug']
        ];
    }
    
    echo json_encode($formattedResults);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Search failed.']);
}
?>
