<?php
// Iphilo Fragrance Footer Include
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
?>
    <footer class="bg-dark text-white pt-5 pb-3">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="fw-bold text-uppercase mb-4"><?php echo SITE_NAME; ?></h5>
                    <p class="small text-muted mb-4"><?php echo get_site_setting('company_bio'); ?></p>
                    <div class="social-icons">
                        <?php foreach (get_all_social_links() as $link): ?>
                            <a href="<?php echo $link['url']; ?>" class="text-white me-3 fs-5" target="_blank"><i class="<?php echo $link['icon_class']; ?>"></i></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="fw-bold text-uppercase mb-4">Quick Links</h5>
                    <ul class="list-unstyled small">
                        <li><a href="index.php" class="text-white text-decoration-none">Home</a></li>
                        <li><a href="shop.php" class="text-white text-decoration-none">Shop</a></li>
                        <li><a href="about.php" class="text-white text-decoration-none">About Us</a></li>
                        <li><a href="specials.php" class="text-white text-decoration-none">Specials</a></li>
                        <li><a href="contact.php" class="text-white text-decoration-none">Contact Us</a></li>
                        <li><a href="faq.php" class="text-white text-decoration-none">FAQs</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="fw-bold text-uppercase mb-4">Information</h5>
                    <ul class="list-unstyled small">
                        <li><a href="reviews.php" class="text-white text-decoration-none">Reviews</a></li>
                        <li><a href="order-tracking.php" class="text-white text-decoration-none">Order Tracking</a></li>
                        <li><a href="privacy-policy.php" class="text-white text-decoration-none">Privacy Policy</a></li>
                        <li><a href="terms-conditions.php" class="text-white text-decoration-none">Terms & Conditions</a></li>
                        <li><a href="shipping-policy.php" class="text-white text-decoration-none">Shipping Policy</a></li>
                        <li><a href="refund-policy.php" class="text-white text-decoration-none">Refund Policy</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="fw-bold text-uppercase mb-4">Contact Info</h5>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2 text-primary"></i> <?php echo get_site_setting('physical_address'); ?></li>
                        <li class="mb-2"><i class="fas fa-phone me-2 text-primary"></i> <?php echo get_site_setting('support_number'); ?></li>
                        <li class="mb-2"><i class="fas fa-envelope me-2 text-primary"></i> <?php echo get_site_setting('contact_email'); ?></li>
                        <li class="mb-2"><i class="fab fa-whatsapp me-2 text-primary"></i> <a href="<?php echo WHATSAPP_LINK; ?>" class="text-white text-decoration-none">WhatsApp Us</a></li>
                    </ul>
                    <div class="newsletter-form mt-4">
                        <h6 class="fw-bold text-uppercase mb-3">Newsletter Subscription</h6>
                        <form action="api/newsletter.php" method="POST" class="d-flex">
                            <input type="email" name="email" class="form-control form-control-sm rounded-0 border-0" placeholder="Enter your email" required>
                            <button type="submit" class="btn btn-primary btn-sm rounded-0 px-3">Subscribe</button>
                        </form>
                    </div>
                </div>
            </div>
            <hr class="my-4 bg-secondary">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="small text-muted mb-0">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved. Established <?php echo ESTABLISHED_DATE; ?>.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="payment-methods">
                        <i class="fab fa-cc-visa me-2 fs-3 text-white"></i>
                        <i class="fab fa-cc-mastercard me-2 fs-3 text-white"></i>
                        <i class="fas fa-university fs-3 text-white" title="EFT / Bank Transfer"></i>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
