<?php
// Iphilo Fragrance Admin Dashboard
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if not logged in
if (!is_admin_logged_in()) {
    redirect('login.php');
}

// Fetch Stats
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(total) FROM orders WHERE status != 'cancelled'")->fetchColumn();
$total_customers = $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
$pending_reviews = $pdo->query("SELECT COUNT(*) FROM product_reviews WHERE status = 'pending'")->fetchColumn();

// Fetch Recent Orders
$recent_orders = $pdo->query("SELECT o.*, c.first_name, c.last_name FROM orders o LEFT JOIN customers c ON o.customer_id = c.id ORDER BY o.created_at DESC LIMIT 5")->fetchAll();

// Fetch Low Stock Products
$low_stock = $pdo->query("SELECT * FROM products WHERE stock_quantity <= 5 AND is_active = 1 LIMIT 5")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Iphilo Fragrance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background-color: #f4f7f6; }
        .sidebar { min-height: 100vh; width: 250px; background: #2c3e50; color: white; position: fixed; }
        .sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 15px 25px; border-left: 4px solid transparent; transition: all 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: rgba(255,255,255,0.1); border-left-color: var(--primary-color); }
        .sidebar .nav-link i { width: 20px; margin-right: 10px; }
        .main-content { margin-left: 250px; padding: 40px; }
        .stat-card { border-radius: 10px; border: none; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .bg-gradient-primary { background: linear-gradient(45deg, #4e73df, #224abe); }
        .bg-gradient-success { background: linear-gradient(45deg, #1cc88a, #13855c); }
        .bg-gradient-info { background: linear-gradient(45deg, #36b9cc, #258391); }
        .bg-gradient-warning { background: linear-gradient(45deg, #f6c23e, #dda20a); }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar d-none d-lg-block">
        <div class="p-4 text-center">
            <img src="../assets/images/logo.png" alt="Iphilo Logo" height="40" class="mb-2">
            <h5 class="fw-bold text-uppercase mb-0">Iphilo Admin</h5>
        </div>
        <nav class="nav flex-column mt-4">
            <a href="dashboard.php" class="nav-link active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="products.php" class="nav-link"><i class="fas fa-box"></i> Products</a>
            <a href="orders.php" class="nav-link"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="customers.php" class="nav-link"><i class="fas fa-users"></i> CRM</a>
            <a href="reviews.php" class="nav-link"><i class="fas fa-star"></i> Reviews <span class="badge bg-danger ms-1"><?php echo $pending_reviews; ?></span></a>
            <a href="reports.php" class="nav-link"><i class="fas fa-chart-line"></i> Reports</a>
            <a href="settings.php" class="nav-link"><i class="fas fa-cog"></i> Settings</a>
            <a href="logout.php" class="nav-link text-danger mt-5"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold mb-1">Dashboard</h2>
                <p class="text-muted small mb-0">Welcome back, <?php echo $_SESSION['admin_name']; ?></p>
            </div>
            <div class="user-info d-flex align-items-center">
                <div class="text-end me-3">
                    <p class="small fw-bold mb-0"><?php echo $_SESSION['admin_name']; ?></p>
                    <p class="small text-muted mb-0"><?php echo ucfirst($_SESSION['admin_role']); ?></p>
                </div>
                <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                    <?php echo strtoupper(substr($_SESSION['admin_name'], 0, 1)); ?>
                </div>
            </div>
        </header>

        <!-- Stats Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card stat-card bg-gradient-primary text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="small text-uppercase mb-1 opacity-75">Total Orders</p>
                            <h3 class="fw-bold mb-0"><?php echo $total_orders; ?></h3>
                        </div>
                        <i class="fas fa-shopping-bag fs-1 opacity-25"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-gradient-success text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="small text-uppercase mb-1 opacity-75">Revenue</p>
                            <h3 class="fw-bold mb-0"><?php echo format_currency($total_revenue); ?></h3>
                        </div>
                        <i class="fas fa-money-bill-wave fs-1 opacity-25"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-gradient-info text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="small text-uppercase mb-1 opacity-75">Customers</p>
                            <h3 class="fw-bold mb-0"><?php echo $total_customers; ?></h3>
                        </div>
                        <i class="fas fa-users fs-1 opacity-25"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-gradient-warning text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="small text-uppercase mb-1 opacity-75">Pending Reviews</p>
                            <h3 class="fw-bold mb-0"><?php echo $pending_reviews; ?></h3>
                        </div>
                        <i class="fas fa-star fs-1 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Recent Orders -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4 bg-white h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Recent Orders</h5>
                        <a href="orders.php" class="btn btn-outline-primary btn-sm px-3">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr class="small text-muted text-uppercase">
                                    <th scope="col" class="py-3">Order #</th>
                                    <th scope="col" class="py-3">Customer</th>
                                    <th scope="col" class="py-3">Amount</th>
                                    <th scope="col" class="py-3">Status</th>
                                    <th scope="col" class="py-3 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td class="py-3 fw-bold"><?php echo $order['order_number']; ?></td>
                                        <td class="py-3 small"><?php echo $order['first_name'] ? $order['first_name'] . ' ' . $order['last_name'] : 'Guest'; ?></td>
                                        <td class="py-3 fw-bold"><?php echo format_currency($order['total']); ?></td>
                                        <td class="py-3">
                                            <span class="badge rounded-pill bg-light text-dark border px-3"><?php echo ucfirst($order['status']); ?></span>
                                        </td>
                                        <td class="py-3 text-end">
                                            <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-link text-primary p-0"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alerts -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 bg-white h-100">
                    <h5 class="fw-bold mb-4">Low Stock Alerts</h5>
                    <?php if (count($low_stock) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($low_stock as $product): ?>
                                <div class="list-group-item px-0 py-3 border-0 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="product-img me-3 bg-light rounded" style="width: 40px; height: 40px; overflow: hidden;">
                                            <img src="../<?php echo get_product_image($product['id']); ?>" class="img-fluid w-100 h-100 object-fit-cover">
                                        </div>
                                        <div>
                                            <p class="small fw-bold mb-0"><?php echo $product['name']; ?></p>
                                            <p class="small text-muted mb-0">Qty: <span class="text-danger fw-bold"><?php echo $product['stock_quantity']; ?></span></p>
                                        </div>
                                    </div>
                                    <a href="products.php?action=edit&id=<?php echo $product['id']; ?>" class="btn btn-outline-dark btn-sm px-3">Restock</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fs-1 text-success opacity-25 mb-3"></i>
                            <p class="text-muted small">All products are well stocked.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
