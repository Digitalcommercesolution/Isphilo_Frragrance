<?php
// Iphilo Fragrance Checkout Page
$page_title = "Checkout";
require_once __DIR__ . '/includes/header.php';

// Redirect to cart if empty
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    redirect('cart.php');
}

$cart_items = $_SESSION['cart'];
$subtotal = get_cart_total();
$shipping = 0; // Shipping to be calculated
$total = $subtotal; // Total is currently just the subtotal

// Pre-fill user data if logged in
$customer = null;
if (is_customer_logged_in()) {
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->execute([$_SESSION['customer_id']]);
    $customer = $stmt->fetch();
}
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <h1 class="fw-bold mb-5">Checkout</h1>
            
            <form action="api/checkout.php" method="POST" enctype="multipart/form-data" id="checkoutForm">
                <!-- Customer Details -->
                <div class="card shadow-sm border-0 mb-4 p-4 bg-light">
                    <h5 class="fw-bold mb-4">1. Customer Details</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label small fw-bold">First Name</label>
                            <input type="text" name="first_name" id="first_name" class="form-control" value="<?php echo $customer ? $customer['first_name'] : ''; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label small fw-bold">Last Name</label>
                            <input type="text" name="last_name" id="last_name" class="form-control" value="<?php echo $customer ? $customer['last_name'] : ''; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label small fw-bold">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?php echo $customer ? $customer['email'] : ''; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label small fw-bold">Phone Number</label>
                            <input type="tel" name="phone" id="phone" class="form-control" value="<?php echo $customer ? $customer['phone'] : ''; ?>" required>
                        </div>
                    </div>
                </div>
                
                <!-- Shipping Address -->
                <div class="card shadow-sm border-0 mb-4 p-4 bg-light">
                    <h5 class="fw-bold mb-4">2. Shipping Address</h5>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="address_line1" class="form-label small fw-bold">Address Line 1</label>
                            <input type="text" name="address_line1" id="address_line1" class="form-control" placeholder="House/Apartment Number, Street Name" required>
                        </div>
                        <div class="col-12">
                            <label for="address_line2" class="form-label small fw-bold">Address Line 2 (Optional)</label>
                            <input type="text" name="address_line2" id="address_line2" class="form-control" placeholder="Suburb/District">
                        </div>
                        <div class="col-md-6">
                            <label for="city" class="form-label small fw-bold">City</label>
                            <input type="text" name="city" id="city" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="state" class="form-label small fw-bold">Province/State</label>
                            <input type="text" name="state" id="state" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label for="postal_code" class="form-label small fw-bold">Postal Code</label>
                            <input type="text" name="postal_code" id="postal_code" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Method -->
                <div class="card shadow-sm border-0 mb-4 p-4 bg-light">
                    <h5 class="fw-bold mb-4">3. Payment Method</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-check card p-3 border-1 bg-white mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_card" value="card" checked>
                                <label class="form-check-label ms-2 d-flex justify-content-between align-items-center" for="payment_card">
                                    <span>Credit/Debit Card</span>
                                    <div class="payment-icons">
                                        <i class="fab fa-cc-visa me-1 fs-4 text-primary"></i>
                                        <i class="fab fa-cc-mastercard fs-4 text-danger"></i>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check card p-3 border-1 bg-white mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_eft" value="eft">
                                <label class="form-check-label ms-2 d-flex justify-content-between align-items-center" for="payment_eft">
                                    <span>EFT / Bank Transfer</span>
                                    <i class="fas fa-university fs-4 text-muted"></i>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- EFT Details (Hidden by default) -->
                    <div id="eft_details" class="mt-4 d-none">
                        <div class="alert alert-info py-3 px-4 border-0 rounded-0">
                            <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i> Bank Details for EFT</h6>
                            <p class="small mb-1">Bank: <strong><?php echo get_site_setting('bank_name'); ?></strong></p>
                            <p class="small mb-1">Account Holder: <strong><?php echo get_site_setting('account_holder'); ?></strong></p>
                            <p class="small mb-1">Account Number: <strong><?php echo get_site_setting('account_number'); ?></strong></p>
                            <p class="small mb-1">Branch Code: <strong><?php echo get_site_setting('branch_code'); ?></strong></p>
                            <p class="small mt-3 text-muted">Please use your Order Number as the reference.</p>
                        </div>
                        
                        <div class="mt-4">
                            <label for="payment_proof" class="form-label small fw-bold">Upload Proof of Payment (Optional now, can be done later)</label>
                            <input type="file" name="payment_proof" id="payment_proof" class="form-control" accept="image/*,application/pdf">
                            <p class="small text-muted mt-1">Accepted formats: JPG, PNG, PDF (Max 5MB)</p>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold text-uppercase mb-5">Place Order & Pay <?php echo format_currency($total); ?></button>
            </form>
        </div>
        
        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 p-4 bg-light sticky-top" style="top: 100px;">
                <h5 class="fw-bold mb-4">Order Summary</h5>
                <div class="cart-items-preview mb-4">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <div class="product-thumb me-2" style="width: 50px; height: 50px; overflow: hidden; border: 1px solid #ddd;">
                                    <img src="<?php echo get_product_image($item['id']); ?>" alt="<?php echo $item['name']; ?>" class="img-fluid w-100 h-100 object-fit-cover">
                                </div>
                                <div>
                                    <p class="small fw-bold mb-0"><?php echo $item['name']; ?></p>
                                    <p class="small text-muted mb-0">Qty: <?php echo $item['quantity']; ?></p>
                                </div>
                            </div>
                            <span class="small fw-bold"><?php echo format_currency($item['price'] * $item['quantity']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <hr class="my-4">
                
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small">Subtotal</span>
                    <span class="fw-bold small"><?php echo format_currency($subtotal); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small">Shipping</span>
                    <span class="text-primary small fw-bold uppercase">To be calculated</span>
                </div>
                <hr class="my-4">
                <div class="d-flex justify-content-between mb-4 fs-5 fw-bold">
                    <span>Estimated Total</span>
                    <span class="text-primary"><?php echo format_currency($total); ?></span>
                </div>
                
                <div class="text-center mt-4">
                    <p class="small text-muted mb-0"><i class="fas fa-lock me-1"></i> Secure and encrypted payment processing.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Toggle EFT details visibility
    $('input[name="payment_method"]').on('change', function() {
        if ($(this).val() === 'eft') {
            $('#eft_details').removeClass('d-none');
        } else {
            $('#eft_details').addClass('d-none');
        }
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
