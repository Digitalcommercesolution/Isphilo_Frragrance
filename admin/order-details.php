<?php
// Iphilo Fragrance Admin Order Details
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if not logged in
if (!is_admin_logged_in()) {
    redirect('login.php');
}

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($order_id <= 0) {
    redirect('orders.php');
}

// Fetch Order Details
$stmt = $pdo->prepare("SELECT o.*, c.first_name, c.last_name, c.email as customer_email, c.phone as customer_phone FROM orders o LEFT JOIN customers c ON o.customer_id = c.id WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    redirect('orders.php');
}

// Fetch Order Items
$stmt = $pdo->prepare("SELECT oi.*, p.name as product_name, p.slug FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll();

// Fetch Payment Proof if any
$stmt = $pdo->prepare("SELECT * FROM payment_proofs WHERE order_id = ? ORDER BY uploaded_at DESC LIMIT 1");
$stmt->execute([$order_id]);
$payment_proof = $stmt->fetch();

// Handle Status/Shipping Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = sanitize($_POST['status']);
    $shipping_cost = (float)$_POST['shipping_cost'];
    
    // Recalculate total
    $new_total = $order['subtotal'] - $order['discount'] + $shipping_cost;
    
    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = ?, shipping_cost = ?, total = ? WHERE id = ?");
        $stmt->execute([$new_status, $shipping_cost, $new_total, $order_id]);
        
        // Refresh order data
        $stmt = $pdo->prepare("SELECT o.*, c.first_name, c.last_name, c.email as customer_email, c.phone as customer_phone FROM orders o LEFT JOIN customers c ON o.customer_id = c.id WHERE o.id = ?");
        $stmt->execute([$order_id]);
        $order = $stmt->fetch();
        
        $success_msg = "Order updated successfully.";
    } catch (PDOException $e) {
        $error_msg = "Error updating order: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?php echo $order['order_number']; ?> | Iphilo Admin</title>
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
    <!-- Sidebar (Same as orders.php) -->
    <div class="sidebar d-none d-lg-block">
        <div class="p-4 text-center">
            <h5 class="fw-bold text-uppercase mb-0">Iphilo Admin</h5>
        </div>
        <nav class="nav flex-column mt-4">
            <a href="dashboard.php" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="products.php" class="nav-link"><i class="fas fa-box"></i> Products</a>
            <a href="orders.php" class="nav-link active"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="logout.php" class="nav-link text-danger mt-5"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <a href="orders.php" class="text-decoration-none small text-muted mb-2 d-block"><i class="fas fa-arrow-left me-1"></i> Back to Orders</a>
                <h2 class="fw-bold mb-1">Order #<?php echo $order['order_number']; ?></h2>
                <p class="text-muted small mb-0">Placed on <?php echo date('d M Y, H:i', strtotime($order['created_at'])); ?></p>
            </div>
            <div>
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
                <span class="badge rounded-pill <?php echo $badge_class; ?> px-4 py-2 fs-6"><?php echo ucfirst($order['status']); ?></span>
            </div>
        </header>

        <?php if (isset($success_msg)): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <?php echo $success_msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <!-- Order Items -->
                <div class="card border-0 shadow-sm p-4 bg-white mb-4">
                    <h5 class="fw-bold mb-4">Order Items</h5>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr class="small text-muted text-uppercase">
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="product-thumb me-3" style="width: 50px; height: 50px; overflow: hidden; border: 1px solid #eee;">
                                                    <img src="<?php echo get_product_image($item['product_id']); ?>" alt="" class="img-fluid w-100 h-100 object-fit-cover">
                                                </div>
                                                <div class="fw-bold small"><?php echo $item['product_name']; ?></div>
                                            </div>
                                        </td>
                                        <td class="small"><?php echo format_currency($item['price']); ?></td>
                                        <td class="small"><?php echo $item['quantity']; ?></td>
                                        <td class="text-end fw-bold"><?php echo format_currency($item['price'] * $item['quantity']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="row mt-4 justify-content-end">
                        <div class="col-md-5">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Subtotal</span>
                                <span class="small fw-bold"><?php echo format_currency($order['subtotal']); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Discount</span>
                                <span class="small fw-bold text-danger">-<?php echo format_currency($order['discount']); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Shipping</span>
                                <span class="small fw-bold"><?php echo format_currency($order['shipping_cost']); ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-0">
                                <span class="fw-bold">Total</span>
                                <span class="fw-bold text-primary fs-5"><?php echo format_currency($order['total']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Update Order Form -->
                <div class="card border-0 shadow-sm p-4 bg-white">
                    <h5 class="fw-bold mb-4">Update Order</h5>
                    <form action="order-details.php?id=<?php echo $order_id; ?>" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Order Status</label>
                                <select name="status" class="form-select">
                                    <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="paid" <?php echo $order['status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                                    <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Shipping Cost (R)</label>
                                <input type="number" name="shipping_cost" class="form-control" step="0.01" value="<?php echo $order['shipping_cost']; ?>" required>
                                <p class="small text-muted mt-1">Entering a value here will recalculate the order total.</p>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-4 px-4">Save Changes</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Customer Info -->
                <div class="card border-0 shadow-sm p-4 bg-white mb-4">
                    <h5 class="fw-bold mb-4">Customer Info</h5>
                    <div class="mb-3">
                        <p class="small text-muted mb-0">Name</p>
                        <p class="fw-bold"><?php echo $order['first_name'] ? $order['first_name'] . ' ' . $order['last_name'] : 'Guest'; ?></p>
                    </div>
                    <div class="mb-3">
                        <p class="small text-muted mb-0">Email</p>
                        <p class="fw-bold"><?php echo $order['customer_email'] ? $order['customer_email'] : $order['guest_email']; ?></p>
                    </div>
                    <div class="mb-3">
                        <p class="small text-muted mb-0">Phone</p>
                        <p class="fw-bold"><?php echo $order['customer_phone'] ? $order['customer_phone'] : 'N/A'; ?></p>
                    </div>
                    <div class="mb-0">
                        <p class="small text-muted mb-0">Shipping Address</p>
                        <p class="fw-bold small"><?php echo nl2br($order['shipping_address']); ?></p>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="card border-0 shadow-sm p-4 bg-white">
                    <h5 class="fw-bold mb-4">Payment Info</h5>
                    <div class="mb-3">
                        <p class="small text-muted mb-0">Method</p>
                        <p class="fw-bold text-uppercase"><?php echo $order['payment_method']; ?></p>
                    </div>
                    
                    <?php if ($payment_proof): ?>
                        <div class="mb-0">
                            <p class="small text-muted mb-2">Proof of Payment</p>
                            <a href="../assets/uploads/payments/<?php echo $payment_proof['file_path']; ?>" target="_blank" class="btn btn-outline-primary btn-sm w-100"><i class="fas fa-external-link-alt me-2"></i> View Attachment</a>
                        </div>
                    <?php else: ?>
                        <p class="small text-muted fst-italic mb-0">No payment proof uploaded.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
