<?php
// Iphilo Fragrance Header Include
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' | ' : ''; ?><?php echo SITE_NAME; ?> - <?php echo SITE_TAGLINE; ?></title>
    
    <!-- Meta Tags -->
    <meta name="description" content="Iphilo Fragrance offers premium, long-lasting perfumes and colognes. Established in 2018, we provide the best fragrance experience.">
    <meta name="keywords" content="perfumes, colognes, fragrance, luxury scents, Iphilo Fragrance">
    <meta name="author" content="Iphilo Fragrance">
    
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Top Bar -->
    <div class="top-bar bg-dark text-white py-2">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="contact-info small">
                <a href="tel:<?php echo SUPPORT_NUMBER; ?>" class="text-white text-decoration-none me-3"><i class="fas fa-phone me-1"></i> <?php echo SUPPORT_NUMBER; ?></a>
                <a href="mailto:<?php echo CONTACT_EMAIL; ?>" class="text-white text-decoration-none d-none d-md-inline"><i class="fas fa-envelope me-1"></i> <?php echo CONTACT_EMAIL; ?></a>
            </div>
            <div class="social-links d-none d-sm-block">
                <?php foreach (get_all_social_links() as $link): ?>
                    <a href="<?php echo $link['url']; ?>" class="text-white ms-2" target="_blank"><i class="<?php echo $link['icon_class']; ?>"></i></a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="assets/images/logo.png" alt="<?php echo SITE_NAME; ?>" height="40" class="me-2">
                <span class="brand-text fw-bold text-uppercase d-none d-sm-inline"><?php echo SITE_NAME; ?></span>
            </a>
            
            <div class="d-flex align-items-center order-lg-last">
                <a href="cart.php" class="nav-link position-relative me-3">
                    <i class="fas fa-shopping-bag fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary cart-count">
                        <?php echo get_cart_count(); ?>
                    </span>
                </a>
                
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user fs-5"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <?php if (is_customer_logged_in()): ?>
                            <li><a class="dropdown-item" href="customer-dashboard.php">My Account</a></li>
                            <li><a class="dropdown-item" href="order-tracking.php">Track Order</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        <?php else: ?>
                            <li><a class="dropdown-item" href="login.php">Login</a></li>
                            <li><a class="dropdown-item" href="register.php">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="shop.php">Shop</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="specials.php">Specials</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reviews.php">Reviews</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
