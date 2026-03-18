<?php
// Iphilo Fragrance Customer Dashboard Page
$page_title = "My Account Dashboard";
require_once __DIR__ . '/includes/header.php';

// Redirect if not logged in
if (!is_customer_logged_in()) {
    redirect('login.php');
}

$customer_id = $_SESSION['customer_id'];

// Fetch Customer Info
$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$customer_id]);
$customer = $stmt->fetch();

// Fetch Recent Orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$customer_id]);
$recent_orders = $stmt->fetchAll();

// Fetch Wishlist Count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlists WHERE customer_id = ?");
$stmt->execute([$customer_id]);
$wishlist_count = $stmt->fetchColumn();

// Fetch Reviews Count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM product_reviews WHERE customer_id = ?");
$stmt->execute([$customer_id]);
$reviews_count = $stmt->fetchColumn();
?>

<div class="container py-5">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3 mb-5">
            <div class="card shadow-sm border-0 p-4 bg-light text-center mb-4">
                <div class="user-avatar mb-3">
                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fs-1 fw-bold" style="width: 80px; height: 80px;">
                        <?php echo strtoupper(substr($customer['first_name'], 0, 1) . substr($customer['last_name'], 0, 1)); ?>
                    </div>
                </div>
                <h5 class="fw-bold mb-1"><?php echo $customer['first_name'] . ' ' . $customer['last_name']; ?></h5>
                <p class="small text-muted mb-0"><?php echo $customer['email']; ?></p>
            </div>
            
            <div class="card shadow-sm border-0 p-4 bg-light">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3"><a href="customer-dashboard.php" class="text-decoration-none text-primary fw-bold"><i class="fas fa-th-large me-2"></i> Dashboard</a></li>
                    <li class="mb-3"><a href="orders-history.php" class="text-decoration-none text-muted"><i class="fas fa-shopping-bag me-2"></i> My Orders</a></li>
                    <li class="mb-3"><a href="wishlist.php" class="text-decoration-none text-muted"><i class="fas fa-heart me-2"></i> Wishlist</a></li>
                    <li class="mb-3"><a href="addresses.php" class="text-decoration-none text-muted"><i class="fas fa-map-marker-alt me-2"></i> Addresses</a></li>
                    <li class="mb-3"><a href="profile-settings.php" class="text-decoration-none text-muted"><i class="fas fa-user-edit me-2"></i> Profile Settings</a></li>
                    <li class="mb-0"><hr class="my-3"></li>
                    <li><a href="logout.php" class="text-decoration-none text-danger small fw-bold"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Dashboard Content -->
        <div class="col-lg-9">
            <h1 class="fw-bold mb-5">Account Dashboard</h1>
            
            <!-- Statistics Cards -->
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 p-4 bg-white h-100 text-center">
                        <i class="fas fa-shopping-bag fs-2 text-primary mb-3"></i>
                        <h3 class="fw-bold mb-1"><?php echo count($recent_orders); ?></h3>
                        <p class="small text-muted mb-0">Total Orders</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 p-4 bg-white h-100 text-center">
                        <i class="fas fa-heart fs-2 text-danger mb-3"></i>
                        <h3 class="fw-bold mb-1"><?php echo $wishlist_count; ?></h3>
                        <p class="small text-muted mb-0">Wishlist Items</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 p-4 bg-white h-100 text-center">
                        <i class="fas fa-star fs-2 text-warning mb-3"></i>
                        <h3 class="fw-bold mb-1"><?php echo $reviews_count; ?></h3>
                        <p class="small text-muted mb-0">Reviews Submitted</p>
                    </div>
                </div>
            </div>
            
            <!-- Recent Orders Table -->
            <div class="card shadow-sm border-0 p-5 bg-white mb-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Recent Orders</h5>
                    <a href="orders-history.php" class="small text-primary text-decoration-none fw-bold">View All</a>
                </div>
                
                <?php if (count($recent_orders) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr class="small text-muted text-uppercase">
                                    <th scope="col" class="py-3">Order ID</th>
                                    <th scope="col" class="py-3">Date</th>
                                    <th scope="col" class="py-3">Total</th>
                                    <th scope="col" class="py-3">Status</th>
                                    <th scope="col" class="py-3 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td class="py-3 fw-bold"><?php echo $order['order_number']; ?></td>
                                        <td class="py-3"><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                                        <td class="py-3 fw-bold text-primary"><?php echo format_currency($order['total']); ?></td>
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
                                            <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-outline-primary btn-sm px-3">Details</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4 bg-light rounded">
                        <p class="text-muted small mb-0">You haven't placed any orders yet.</p>
                        <a href="shop.php" class="btn btn-link text-primary text-decoration-none fw-bold">Start Shopping <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Quick Links -->
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 p-4 bg-white h-100">
                        <h6 class="fw-bold mb-3"><i class="fas fa-map-marker-alt me-2 text-primary"></i> Default Address</h6>
                        <p class="small text-muted mb-3">You haven't set a default shipping address yet.</p>
                        <a href="addresses.php" class="small text-primary text-decoration-none fw-bold">Manage Addresses</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 p-4 bg-white h-100">
                        <h6 class="fw-bold mb-3"><i class="fas fa-user-edit me-2 text-primary"></i> Account Settings</h6>
                        <p class="small text-muted mb-3">Update your personal information and security settings.</p>
                        <a href="profile-settings.php" class="small text-primary text-decoration-none fw-bold">Edit Profile</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
