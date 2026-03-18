<?php
// Iphilo Fragrance CRM API (Admin Access Only)
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

if (!is_admin_logged_in()) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'customer_summary':
        $customer_id = (int)$_GET['id'];
        
        // Fetch Customer Profile
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
        $stmt->execute([$customer_id]);
        $customer = $stmt->fetch();
        
        // Fetch Total Spend
        $stmt = $pdo->prepare("SELECT SUM(total) FROM orders WHERE customer_id = ? AND status = 'completed'");
        $stmt->execute([$customer_id]);
        $total_spend = $stmt->fetchColumn();
        
        // Fetch Most Purchased Category
        $stmt = $pdo->prepare("SELECT c.name, COUNT(*) as count 
                              FROM order_items oi 
                              JOIN products p ON oi.product_id = p.id 
                              JOIN categories c ON p.category_id = c.id 
                              JOIN orders o ON oi.order_id = o.id 
                              WHERE o.customer_id = ? 
                              GROUP BY c.id 
                              ORDER BY count DESC 
                              LIMIT 1");
        $stmt->execute([$customer_id]);
        $fav_category = $stmt->fetch();
        
        // Fetch Recent Interactions
        $stmt = $pdo->prepare("SELECT * FROM customer_activity_logs WHERE customer_id = ? ORDER BY created_at DESC LIMIT 5");
        $stmt->execute([$customer_id]);
        $interactions = $stmt->fetchAll();
        
        echo json_encode([
            'profile' => $customer,
            'total_spend' => $total_spend,
            'favorite_category' => $fav_category,
            'recent_interactions' => $interactions
        ]);
        break;
        
    case 'segment_loyal':
        // Customers with more than 3 completed orders
        $stmt = $pdo->query("SELECT c.*, COUNT(o.id) as order_count 
                             FROM customers c 
                             JOIN orders o ON c.id = o.customer_id 
                             WHERE o.status = 'completed' 
                             GROUP BY c.id 
                             HAVING order_count >= 3");
        echo json_encode($stmt->fetchAll());
        break;
        
    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
?>
