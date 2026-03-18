<?php
// Iphilo Fragrance Order Tracking Page
$page_title = "Track Your Order";
require_once __DIR__ . '/includes/header.php';

$order_number = isset($_GET['order_id']) ? sanitize($_GET['order_id']) : '';
$email = isset($_GET['email']) ? sanitize($_GET['email']) : '';

$order = null;
$error = '';

if (!empty($order_number)) {
    // Fetch Order Details
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_number = ? AND (guest_email = ? OR customer_id IN (SELECT id FROM customers WHERE email = ?))");
    $stmt->execute([$order_number, $email, $email]);
    $order = $stmt->fetch();
    
    if (!$order) {
        $error = "Order not found. Please check your order number and email address.";
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="fw-bold mb-5 text-center">Track Your Order</h1>
            
            <div class="card shadow-sm border-0 p-5 bg-light mb-5">
                <form action="order-tracking.php" method="GET" class="row g-3">
                    <div class="col-md-5">
                        <label for="order_id" class="form-label small fw-bold">Order Number</label>
                        <input type="text" name="order_id" id="order_id" class="form-control" placeholder="e.g. IPH-12345678" value="<?php echo $order_number; ?>" required>
                    </div>
                    <div class="col-md-5">
                        <label for="email" class="form-label small fw-bold">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="Your registered email" value="<?php echo $email; ?>" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 py-2">Track</button>
                    </div>
                </form>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger py-2 px-3 small rounded-0 mb-4 text-center"><?php echo $error; ?></div>
            <?php elseif ($order): ?>
                <!-- Order Status Timeline -->
                <div class="card shadow-sm border-0 p-5 bg-white mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-5">
                        <h5 class="fw-bold mb-0">Order Status: <span class="text-primary"><?php echo ucfirst($order['status']); ?></span></h5>
                        <span class="small text-muted">Order Date: <?php echo date('d M Y', strtotime($order['created_at'])); ?></span>
                    </div>
                    
                    <div class="order-timeline position-relative mb-5 py-4">
                        <div class="progress position-absolute w-100 top-50 start-0 translate-middle-y" style="height: 4px;">
                            <?php 
                            $progress = 0;
                            switch($order['status']) {
                                case 'pending': $progress = 10; break;
                                case 'paid': $progress = 30; break;
                                case 'processing': $progress = 50; break;
                                case 'shipped': $progress = 80; break;
                                case 'completed': $progress = 100; break;
                            }
                            ?>
                            <div class="progress-bar bg-primary" style="width: <?php echo $progress; ?>%;"></div>
                        </div>
                        
                        <div class="d-flex justify-content-between position-relative z-index-1">
                            <div class="timeline-step text-center">
                                <div class="step-icon rounded-circle bg-<?php echo $progress >= 10 ? 'primary' : 'light'; ?> text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                                <p class="small fw-bold mb-0">Pending</p>
                            </div>
                            <div class="timeline-step text-center">
                                <div class="step-icon rounded-circle bg-<?php echo $progress >= 30 ? 'primary' : 'light'; ?> text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <p class="small fw-bold mb-0">Paid</p>
                            </div>
                            <div class="timeline-step text-center">
                                <div class="step-icon rounded-circle bg-<?php echo $progress >= 50 ? 'primary' : 'light'; ?> text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                                    <i class="fas fa-cog"></i>
                                </div>
                                <p class="small fw-bold mb-0">Processing</p>
                            </div>
                            <div class="timeline-step text-center">
                                <div class="step-icon rounded-circle bg-<?php echo $progress >= 80 ? 'primary' : 'light'; ?> text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                                    <i class="fas fa-shipping-fast"></i>
                                </div>
                                <p class="small fw-bold mb-0">Shipped</p>
                            </div>
                            <div class="timeline-step text-center">
                                <div class="step-icon rounded-circle bg-<?php echo $progress >= 100 ? 'primary' : 'light'; ?> text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                                    <i class="fas fa-check"></i>
                                </div>
                                <p class="small fw-bold mb-0">Completed</p>
                            </div>
                        </div>
                    </div>
                    
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3">Shipping Address</h6>
                                <p class="small text-muted mb-0"><?php echo nl2br($order['shipping_address']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3">Order Details</h6>
                                <p class="small text-muted mb-1">Subtotal: <?php echo format_currency($order['subtotal']); ?></p>
                                <p class="small text-muted mb-1">Shipping: <span class="text-primary fw-bold uppercase">To be calculated</span></p>
                                <p class="small text-muted mb-3 italic">Our team in Berea, Durban is currently calculating the shipping tariffs for your delivery.</p>
                                <p class="small fw-bold text-primary mb-0 mt-2">Estimated Total: <?php echo format_currency($order['total']); ?></p>
                            </div>
                        </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
