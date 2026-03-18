<?php
// Iphilo Fragrance Cart Page
$page_title = "Your Shopping Cart";
require_once __DIR__ . '/includes/header.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart_items = $_SESSION['cart'];
$subtotal = get_cart_total();
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mb-5">
            <h1 class="fw-bold mb-4">Your Shopping Cart</h1>
            
            <?php if (count($cart_items) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <thead>
                            <tr class="border-bottom text-muted small text-uppercase">
                                <th scope="col" class="py-3">Product</th>
                                <th scope="col" class="py-3">Price</th>
                                <th scope="col" class="py-3">Quantity</th>
                                <th scope="col" class="py-3">Total</th>
                                <th scope="col" class="py-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $id => $item): ?>
                                <tr class="border-bottom">
                                    <td class="py-4">
                                        <div class="d-flex align-items-center">
                                            <div class="product-image me-3 shadow-sm rounded" style="width: 80px; height: 80px; overflow: hidden;">
                                                <img src="<?php echo get_product_image($id); ?>" alt="<?php echo $item['name']; ?>" class="img-fluid w-100 h-100 object-fit-cover">
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-1"><?php echo $item['name']; ?></h6>
                                                <p class="text-muted small mb-0"><?php echo $item['category']; ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4"><?php echo format_currency($item['price']); ?></td>
                                    <td class="py-4">
                                        <form action="api/cart.php" method="POST" class="d-flex align-items-center">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                                            <input type="number" name="quantity" class="form-control text-center" value="<?php echo $item['quantity']; ?>" min="1" max="10" style="width: 60px;" onchange="this.form.submit()">
                                        </form>
                                    </td>
                                    <td class="py-4 fw-bold text-primary"><?php echo format_currency($item['price'] * $item['quantity']); ?></td>
                                    <td class="py-4 text-end">
                                        <a href="api/cart.php?action=remove&id=<?php echo $id; ?>" class="text-danger text-decoration-none small"><i class="fas fa-trash-alt me-1"></i> Remove</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="shop.php" class="btn btn-link text-primary text-decoration-none fw-bold"><i class="fas fa-arrow-left me-2"></i> Continue Shopping</a>
                    <a href="api/cart.php?action=clear" class="btn btn-link text-danger text-decoration-none small">Clear All</a>
                </div>
                
            <?php else: ?>
                <div class="text-center py-5 bg-light rounded shadow-sm">
                    <i class="fas fa-shopping-bag fs-1 text-muted mb-3"></i>
                    <h3>Your cart is empty</h3>
                    <p class="text-muted">It looks like you haven't added anything to your cart yet.</p>
                    <a href="shop.php" class="btn btn-primary mt-3">Browse Our Collection</a>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Order Summary -->
        <?php if (count($cart_items) > 0): ?>
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 p-4 bg-light">
                    <h5 class="fw-bold mb-4">Order Summary</h5>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-bold"><?php echo format_currency($subtotal); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Shipping</span>
                        <span class="text-primary small fw-bold">Calculated at checkout</span>
                    </div>
                    <hr class="my-4">
                    <div class="d-flex justify-content-between mb-4 fs-5 fw-bold">
                        <span>Total</span>
                        <span class="text-primary"><?php echo format_currency($subtotal); ?></span>
                    </div>
                    
                    <div class="promo-code mb-4">
                        <form action="api/cart.php" method="POST" class="d-flex">
                            <input type="hidden" name="action" value="apply_coupon">
                            <input type="text" name="coupon_code" class="form-control rounded-0" placeholder="Promo code">
                            <button type="submit" class="btn btn-dark rounded-0 px-3">Apply</button>
                        </form>
                    </div>
                    
                    <a href="checkout.php" class="btn btn-primary w-100 py-3 fw-bold text-uppercase">Proceed to Checkout</a>
                    
                    <div class="text-center mt-4">
                        <p class="small text-muted mb-0"><i class="fas fa-lock me-1"></i> Secure and encrypted payment processing.</p>
                        <div class="payment-icons mt-2">
                            <i class="fab fa-cc-visa me-2 fs-4 text-muted"></i>
                            <i class="fab fa-cc-mastercard me-2 fs-4 text-muted"></i>
                            <i class="fas fa-university fs-4 text-muted" title="EFT / Bank Transfer"></i>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
