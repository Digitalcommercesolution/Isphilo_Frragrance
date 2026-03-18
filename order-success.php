<?php
// Iphilo Fragrance Order Success Page
$page_title = "Order Successful";
require_once __DIR__ . '/includes/header.php';

$order_number = isset($_SESSION['last_order_number']) ? $_SESSION['last_order_number'] : '';
$total = isset($_SESSION['last_order_total']) ? $_SESSION['last_order_total'] : 0;

if (empty($order_number)) {
    redirect('index.php');
}

// Clear session variables after showing once
unset($_SESSION['last_order_number']);
unset($_SESSION['last_order_total']);
?>

<div class="container py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="success-icon mb-4">
                <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
            </div>
            <h1 class="fw-bold mb-3">Thank You for Your Order!</h1>
            <p class="lead text-muted mb-5">Your order has been placed successfully and is being processed.</p>
            
            <div class="card shadow-sm border-0 p-5 bg-light mb-5">
                <h5 class="fw-bold mb-4">Order Summary</h5>
                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                    <span class="text-muted">Order Number</span>
                    <span class="fw-bold"><?php echo $order_number; ?></span>
                </div>
                <div class="d-flex justify-content-between mb-4 border-bottom pb-2">
                    <span class="text-muted">Total Amount</span>
                    <span class="fw-bold text-primary"><?php echo format_currency($total); ?></span>
                </div>
                <p class="small text-muted mb-0">A confirmation email has been sent to your email address.</p>
            </div>
            
            <div class="alert alert-info py-3 px-4 border-0 rounded-0 mb-5">
                <h6 class="fw-bold mb-2"><i class="fas fa-info-circle me-2"></i> Next Steps</h6>
                <p class="small mb-2">We have received your order! Since we are shipping from <strong>Berea, Durban</strong> to your location, our team will now calculate the exact shipping tariffs for your delivery.</p>
                <p class="small mb-0">You will receive an updated invoice with the shipping costs shortly. Once payment is confirmed, your signature scent will be on its way!</p>
            </div>
            
            <div class="d-flex justify-content-center gap-3">
                <a href="order-tracking.php?order_id=<?php echo $order_number; ?>" class="btn btn-primary px-4 py-2">Track Order</a>
                <a href="shop.php" class="btn btn-outline-primary px-4 py-2">Continue Shopping</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
