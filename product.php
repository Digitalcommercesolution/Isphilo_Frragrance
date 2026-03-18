<?php
// Iphilo Fragrance Product Details Page
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($slug)) {
    redirect('shop.php');
}

// Fetch Product Details
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.id as category_id FROM products p JOIN categories c ON p.category_id = c.id WHERE p.slug = ? AND p.is_active = 1");
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    redirect('shop.php');
}

$page_title = $product['name'];
require_once __DIR__ . '/includes/header.php';

// Fetch Related Products
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? AND p.id != ? AND p.is_active = 1 LIMIT 4");
$stmt->execute([$product['category_id'], $product['id']]);
$related_products = $stmt->fetchAll();

// Fetch Product Reviews
$stmt = $pdo->prepare("SELECT pr.*, c.first_name, c.last_name FROM product_reviews pr JOIN customers c ON pr.customer_id = c.id WHERE pr.product_id = ? AND pr.status = 'approved' ORDER BY pr.created_at DESC");
$stmt->execute([$product['id']]);
$reviews = $stmt->fetchAll();

// Calculate Average Rating
$avg_rating = 0;
if (count($reviews) > 0) {
    $total_rating = array_sum(array_column($reviews, 'rating'));
    $avg_rating = round($total_rating / count($reviews), 1);
}
?>

<div class="container py-5">
    <div class="row mb-5">
        <!-- Product Images -->
        <div class="col-lg-6 mb-4">
            <div class="product-gallery">
                <div class="main-image shadow-sm mb-3">
                    <img src="<?php echo get_product_image($product['id']); ?>" alt="<?php echo $product['name']; ?>" class="img-fluid w-100 rounded">
                </div>
            </div>
        </div>
        
        <!-- Product Info -->
        <div class="col-lg-6 ps-lg-5">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
                    <li class="breadcrumb-item"><a href="shop.php" class="text-decoration-none text-muted">Shop</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo $product['name']; ?></li>
                </ol>
            </nav>
            
            <h1 class="fw-bold mb-3"><?php echo $product['name']; ?></h1>
            <p class="text-muted small mb-3"><?php echo $product['category_name']; ?></p>
            
            <div class="d-flex align-items-center mb-4">
                <div class="text-primary me-2">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="<?php echo $i <= $avg_rating ? 'fas' : 'far'; ?> fa-star"></i>
                    <?php endfor; ?>
                </div>
                <span class="small text-muted">(<?php echo count($reviews); ?> Reviews)</span>
            </div>
            
            <div class="product-price fs-2 fw-bold mb-4">
                <?php if ($product['sale_price']): ?>
                    <span class="old-price fs-4 fw-normal me-3"><?php echo format_currency($product['price']); ?></span>
                    <span><?php echo format_currency($product['sale_price']); ?></span>
                <?php else: ?>
                    <span><?php echo format_currency($product['price']); ?></span>
                <?php endif; ?>
            </div>
            
            <div class="mb-4">
                <p class="text-muted"><?php echo nl2br($product['description']); ?></p>
            </div>
            
            <?php if ($product['fragrance_notes']): ?>
                <div class="mb-4">
                    <h6 class="fw-bold text-uppercase mb-3">Fragrance Notes</h6>
                    <p class="small text-muted"><?php echo nl2br($product['fragrance_notes']); ?></p>
                </div>
            <?php endif; ?>
            
            <div class="mb-5">
                <form action="api/cart.php" method="POST" class="row g-3">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                    <div class="col-auto">
                        <label for="quantity" class="visually-hidden">Quantity</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>" style="width: 80px;">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary px-5 add-to-cart" data-product-id="<?php echo $product['id']; ?>">Add to Cart</button>
                    </div>
                </form>
            </div>
            
            <div class="border-top pt-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-primary me-2"></i>
                            <span class="small">In Stock (<?php echo $product['stock_quantity']; ?> available)</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-shipping-fast text-primary me-2"></i>
                            <span class="small">Shipping calculated at checkout</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-undo text-primary me-2"></i>
                            <span class="small">7-Day Easy Returns</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-lock text-primary me-2"></i>
                            <span class="small">Secure Payment</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Reviews Section -->
    <section class="py-5 border-top">
        <div class="row">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-4">Customer Reviews</h3>
                
                <?php if (count($reviews) > 0): ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="card border-0 shadow-sm mb-4 p-4 bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="fw-bold mb-0"><?php echo $review['first_name'] . ' ' . $review['last_name']; ?></h6>
                                    <div class="text-primary small mt-1">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="<?php echo $i <= $review['rating'] ? 'fas' : 'far'; ?> fa-star"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <span class="small text-muted"><?php echo date('d M Y', strtotime($review['created_at'])); ?></span>
                            </div>
                            <p class="mb-0"><?php echo nl2br($review['comment']); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5 bg-light rounded">
                        <p class="text-muted mb-0">No reviews yet for this product. Be the first to share your experience!</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 p-4 bg-light mt-5 mt-lg-0">
                    <h5 class="fw-bold mb-4">Write a Review</h5>
                    <?php if (is_customer_logged_in()): ?>
                        <form action="api/reviews.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <div class="mb-3">
                                <label class="form-label">Rating</label>
                                <div class="rating-input fs-4 text-primary">
                                    <i class="far fa-star rating-star" data-rating="1"></i>
                                    <i class="far fa-star rating-star" data-rating="2"></i>
                                    <i class="far fa-star rating-star" data-rating="3"></i>
                                    <i class="far fa-star rating-star" data-rating="4"></i>
                                    <i class="far fa-star rating-star" data-rating="5"></i>
                                    <input type="hidden" name="rating" id="rating_value" value="5">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="comment" class="form-label">Your Review</label>
                                <textarea name="comment" id="comment" class="form-control" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Submit Review</button>
                        </form>
                    <?php else: ?>
                        <div class="text-center">
                            <p class="text-muted small">Please login to write a review.</p>
                            <a href="login.php" class="btn btn-outline-primary btn-sm px-4">Login Now</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Related Products -->
    <?php if (count($related_products) > 0): ?>
        <section class="py-5 border-top">
            <h3 class="fw-bold mb-5">You May Also Like</h3>
            <div class="row">
                <?php foreach ($related_products as $related): ?>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="product-card shadow-sm h-100">
                            <div class="product-image-container">
                                <a href="product.php?slug=<?php echo $related['slug']; ?>">
                                    <img src="<?php echo get_product_image($related['id']); ?>" alt="<?php echo $related['name']; ?>">
                                </a>
                            </div>
                            <div class="product-info">
                                <p class="text-muted small mb-1"><?php echo $related['category_name']; ?></p>
                                <h5 class="product-title"><?php echo $related['name']; ?></h5>
                                <div class="product-price">
                                    <span><?php echo format_currency($related['price']); ?></span>
                                </div>
                                <div class="mt-3">
                                    <a href="api/cart.php?action=add&id=<?php echo $related['id']; ?>" class="btn btn-primary btn-sm add-to-cart" data-product-id="<?php echo $related['id']; ?>">Add to Cart</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    // Rating star interaction
    $('.rating-star').on('click', function() {
        var rating = $(this).data('rating');
        $('#rating_value').val(rating);
        $('.rating-star').removeClass('fas').addClass('far');
        $('.rating-star').each(function() {
            if ($(this).data('rating') <= rating) {
                $(this).removeClass('far').addClass('fas');
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
