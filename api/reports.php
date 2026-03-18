<?php
// Iphilo Fragrance Reports API (Admin Access Only)
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
    case 'sales_summary':
        // Daily Sales (Last 7 Days)
        $stmt = $pdo->query("SELECT DATE(created_at) as date, SUM(total) as total 
                             FROM orders 
                             WHERE status IN ('paid', 'processing', 'shipped', 'completed') 
                             AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
                             GROUP BY date 
                             ORDER BY date ASC");
        $daily_sales = $stmt->fetchAll();
        
        // Monthly Sales (Last 12 Months)
        $stmt = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total) as total 
                             FROM orders 
                             WHERE status IN ('paid', 'processing', 'shipped', 'completed') 
                             AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH) 
                             GROUP BY month 
                             ORDER BY month ASC");
        $monthly_sales = $stmt->fetchAll();
        
        // Top Selling Products
        $stmt = $pdo->query("SELECT p.name, SUM(oi.quantity) as quantity, SUM(oi.price * oi.quantity) as revenue 
                             FROM order_items oi 
                             JOIN products p ON oi.product_id = p.id 
                             JOIN orders o ON oi.order_id = o.id 
                             WHERE o.status IN ('paid', 'processing', 'shipped', 'completed') 
                             GROUP BY p.id 
                             ORDER BY quantity DESC 
                             LIMIT 5");
        $top_products = $stmt->fetchAll();
        
        // Sales by Category
        $stmt = $pdo->query("SELECT c.name, SUM(oi.price * oi.quantity) as revenue 
                             FROM order_items oi 
                             JOIN products p ON oi.product_id = p.id 
                             JOIN categories c ON p.category_id = c.id 
                             JOIN orders o ON oi.order_id = o.id 
                             WHERE o.status IN ('paid', 'processing', 'shipped', 'completed') 
                             GROUP BY c.id 
                             ORDER BY revenue DESC");
        $category_sales = $stmt->fetchAll();
        
        echo json_encode([
            'daily_sales' => $daily_sales,
            'monthly_sales' => $monthly_sales,
            'top_products' => $top_products,
            'category_sales' => $category_sales
        ]);
        break;
        
    case 'peak_times':
        // Peak Purchase Hours
        $stmt = $pdo->query("SELECT HOUR(created_at) as hour, COUNT(*) as count 
                             FROM orders 
                             WHERE status != 'cancelled' 
                             GROUP BY hour 
                             ORDER BY count DESC");
        echo json_encode($stmt->fetchAll());
        break;
        
    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
?>
