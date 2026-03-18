<?php
// Iphilo Fragrance Homepage
$page_title = "Perfumes and More";
require_once __DIR__ . '/includes/header.php';

// Fetch Featured Products
$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_featured = 1 AND p.is_active = 1 LIMIT 4");
$featured_products = $stmt->fetchAll();

// Fetch Latest Arrivals
$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_active = 1 ORDER BY p.created_at DESC LIMIT 4");
$latest_arrivals = $stmt->fetchAll();

// Fetch Best Sellers
$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_bestseller = 1 AND p.is_active = 1 LIMIT 4");
$best_sellers = $stmt->fetchAll();
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title"><?php echo SITE_NAME; ?></h1>
            <p class="hero-tagline"><?php echo SITE_TAGLINE; ?></p>
            <div class="hero-buttons">
                <a href="shop.php" class="btn btn-primary me-3">Shop Now</a>
                <a href="about.php" class="btn btn-outline-light px-4 py-2 text-uppercase fw-bold">Our Story</a>
            </div>
        </div>
    </div>
</section>

<!-- Brand Introduction -->
<section class="py-5 bg-light">
    <div class="container text-center py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="mb-4">Elegance in Every Drop</h2>
                <p class="lead text-muted mb-5"><?php echo get_site_setting('company_bio'); ?></p>
                <div class="row">
                    <div class="col-md-4 mb-4 mb-md-0">
                        <i class="fas fa-award fs-1 text-primary mb-3"></i>
                        <h5>Premium Quality</h5>
                        <p class="small text-muted">Only the finest ingredients sourced for our unique blends.</p>
                    </div>
                    <div class="col-md-4 mb-4 mb-md-0">
                        <i class="fas fa-hourglass-half fs-1 text-primary mb-3"></i>
                        <h5>Long Lasting</h5>
                        <p class="small text-muted">Fragrances that stay with you from morning to night.</p>
                    </div>
                    <div class="col-md-4">
                        <i class="fas fa-shipping-fast fs-1 text-primary mb-3"></i>
                        <h5>Fast Delivery</h5>
                        <p class="small text-muted">Quick and secure shipping to your doorstep nationwide.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-5">
    <div class="container py-5">
        <div class="section-heading">
            <h2>Featured Collections</h2>
            <p class="text-muted">Hand-picked fragrances for your unique personality.</p>
        </div>
        
        <div class="row">
            <?php foreach ($featured_products as $product): ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="product-card shadow-sm h-100">
                        <span class="product-badge">Featured</span>
                        <div class="product-image-container">
                            <a href="product.php?slug=<?php echo $product['slug']; ?>">
                                <img src="<?php echo get_product_image($product['id']); ?>" alt="<?php echo $product['name']; ?>">
                            </a>
                        </div>
                        <div class="product-info">
                            <p class="text-muted small mb-1"><?php echo $product['category_name']; ?></p>
                            <h5 class="product-title"><?php echo $product['name']; ?></h5>
                            <div class="product-price">
                                <?php if ($product['sale_price']): ?>
                                    <span class="old-price"><?php echo format_currency($product['price']); ?></span>
                                    <span><?php echo format_currency($product['sale_price']); ?></span>
                                <?php else: ?>
                                    <span><?php echo format_currency($product['price']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="mt-3">
                                <a href="api/cart.php?action=add&id=<?php echo $product['id']; ?>" class="btn btn-primary btn-sm add-to-cart" data-product-id="<?php echo $product['id']; ?>">Add to Cart</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="shop.php" class="btn btn-outline-primary">View All Products</a>
        </div>
    </div>
</section>

<!-- Promotional Banner -->
<section class="py-5 bg-dark text-white text-center">
    <div class="container py-4">
        <h2 class="text-white mb-3">Special Offer!</h2>
        <p class="lead mb-4">Get 10% off your first order with code: <span class="badge bg-primary fs-5 px-3 py-2">WELCOME10</span></p>
        <a href="shop.php" class="btn btn-primary">Claim Offer</a>
    </div>
</section>

<!-- Latest Arrivals -->
<section class="py-5 bg-light">
    <div class="container py-5">
        <div class="section-heading">
            <h2>Latest Arrivals</h2>
            <p class="text-muted">Discover our newest additions to the Iphilo family.</p>
        </div>
        
        <div class="row">
            <?php foreach ($latest_arrivals as $product): ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="product-card shadow-sm h-100">
                        <span class="product-badge bg-dark">New</span>
                        <div class="product-image-container">
                            <a href="product.php?slug=<?php echo $product['slug']; ?>">
                                <img src="<?php echo get_product_image($product['id']); ?>" alt="<?php echo $product['name']; ?>">
                            </a>
                        </div>
                        <div class="product-info">
                            <p class="text-muted small mb-1"><?php echo $product['category_name']; ?></p>
                            <h5 class="product-title"><?php echo $product['name']; ?></h5>
                            <div class="product-price">
                                <span><?php echo format_currency($product['price']); ?></span>
                            </div>
                            <div class="mt-3">
                                <a href="api/cart.php?action=add&id=<?php echo $product['id']; ?>" class="btn btn-primary btn-sm add-to-cart" data-product-id="<?php echo $product['id']; ?>">Add to Cart</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Testimonials Preview -->
<section class="py-5">
    <div class="container py-5 text-center">
        <div class="section-heading">
            <h2>What Our Customers Say</h2>
            <p class="text-muted">Real stories from fragrance enthusiasts.</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-4 mb-4">
                <div class="testimonial-card p-4 shadow-sm bg-white h-100">
                    <div class="stars text-primary mb-3">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="fst-italic">"Absolutely stunning fragrances. Midnight Rose is my new signature scent. The quality is exceptional!"</p>
                    <h6 class="fw-bold mt-4">- Sarah M.</h6>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="testimonial-card p-4 shadow-sm bg-white h-100">
                    <div class="stars text-primary mb-3">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="fst-italic">"I've tried many luxury brands, but Iphilo stands out for its longevity and unique scent profiles. Highly recommended."</p>
                    <h6 class="fw-bold mt-4">- James L.</h6>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="testimonial-card p-4 shadow-sm bg-white h-100">
                    <div class="stars text-primary mb-3">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="fst-italic">"Great customer service and fast delivery. The packaging was beautiful, making it a perfect gift."</p>
                    <h6 class="fw-bold mt-4">- Emily R.</h6>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <a href="reviews.php" class="btn btn-link text-primary text-decoration-none fw-bold">Read All Reviews <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
