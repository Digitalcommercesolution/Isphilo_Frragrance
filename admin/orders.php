<?php
// Iphilo Fragrance Admin Orders Management
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if not logged in
if (!is_admin_logged_in()) {
    redirect('login.php');
}

$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : 'all';

// Fetch Orders
$query = "SELECT o.*, c.first_name, c.last_name FROM orders o LEFT JOIN customers c ON o.customer_id = c.id";
if ($status_filter !== 'all') {
    $query .= " WHERE o.status = :status";
}
$query .= " ORDER BY o.created_at DESC";

$stmt = $pdo->prepare($query);
if ($status_filter !== 'all') {
    $stmt->bindParam(':status', $status_filter);
}
$stmt->execute();
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management | Iphilo Admin</title>
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
            <a href="dashboard.php" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="products.php" class="nav-link"><i class="fas fa-box"></i> Products</a>
            <a href="orders.php" class="nav-link active"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="customers.php" class="nav-link"><i class="fas fa-users"></i> CRM</a>
            <a href="reviews.php" class="nav-link"><i class="fas fa-star"></i> Reviews</a>
            <a href="reports.php" class="nav-link"><i class="fas fa-chart-line"></i> Reports</a>
            <a href="settings.php" class="nav-link"><i class="fas fa-cog"></i> Settings</a>
            <a href="logout.php" class="nav-link text-danger mt-5"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold mb-1">Order Management</h2>
                <p class="text-muted small mb-0">View and update customer orders.</p>
            </div>
            <div class="filter-group">
                <form action="orders.php" method="GET" class="d-flex align-items-center">
                    <label class="small text-muted me-2">Status Filter:</label>
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>All Statuses</option>
                        <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="paid" <?php echo $status_filter == 'paid' ? 'selected' : ''; ?>>Paid</option>
                        <option value="processing" <?php echo $status_filter == 'processing' ? 'selected' : ''; ?>>Processing</option>
                        <option value="shipped" <?php echo $status_filter == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                        <option value="completed" <?php echo $status_filter == 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $status_filter == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </form>
            </div>
        </header>

        <!-- Orders List -->
        <div class="card border-0 shadow-sm p-4 bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="small text-muted text-uppercase">
                            <th scope="col" class="py-3">Order ID</th>
                            <th scope="col" class="py-3">Date</th>
                            <th scope="col" class="py-3">Customer</th>
                            <th scope="col" class="py-3">Total</th>
                            <th scope="col" class="py-3">Method</th>
                            <th scope="col" class="py-3">Status</th>
                            <th scope="col" class="py-3 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="py-3 fw-bold"><?php echo $order['order_number']; ?></td>
                                <td class="py-3 small"><?php echo date('d M Y, H:i', strtotime($order['created_at'])); ?></td>
                                <td class="py-3 small">
                                    <?php echo $order['first_name'] ? $order['first_name'] . ' ' . $order['last_name'] : $order['guest_email']; ?>
                                    <?php if (!$order['first_name']): ?><span class="badge bg-secondary ms-1">Guest</span><?php endif; ?>
                                </td>
                                <td class="py-3 fw-bold text-primary"><?php echo format_currency($order['total']); ?></td>
                                <td class="py-3 small text-uppercase"><?php echo $order['payment_method']; ?></td>
                                <td class="py-3">
                                    <?php 
                                    $badge_class = 'bg-secondary';
                                    switch($order['status']) {
                                        case 'pending': $badge_class = 'bg-warning text-dark'; break;
                                        case 'paid': $badge_class = 'bg-info text-white'; break;
                                        case 'processing': $badge_class = 'bg-primary'; break;
                                        case 'shipped': $badge_class = 'bg-info'; break;
                                        case 'completed': $badge_class = 'bg-success'; break;
                                        case 'cancelled': $badge_class = 'bg-danger'; break;
                                    }
                                    ?>
                                    <span class="badge rounded-pill <?php echo $badge_class; ?> px-3"><?php echo ucfirst($order['status']); ?></span>
                                </td>
                                <td class="py-3 text-end">
                                    <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-outline-dark btn-sm px-3">View Details</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
