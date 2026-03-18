<?php
// Iphilo Fragrance Login Page
$page_title = "Customer Login";
require_once __DIR__ . '/includes/header.php';

// Redirect if already logged in
if (is_customer_logged_in()) {
    redirect('customer-dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!verify_csrf_token($csrf_token)) {
        $error = "CSRF verification failed. Please try again.";
    } elseif (login_customer($email, $password)) {
        redirect('customer-dashboard.php');
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-8">
            <div class="card shadow-lg border-0 p-5 bg-white">
                <div class="text-center mb-5">
                    <h2 class="fw-bold text-uppercase mb-2">Welcome Back</h2>
                    <p class="text-muted small">Please login to access your account.</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger py-2 px-3 small rounded-0 mb-4"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form action="login.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <div class="mb-4">
                        <label for="email" class="form-label small fw-bold">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between">
                            <label for="password" class="form-label small fw-bold">Password</label>
                            <a href="forgot-password.php" class="small text-decoration-none text-primary">Forgot?</a>
                        </div>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember">
                        <label class="form-check-label small text-muted" for="remember">Remember me</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold text-uppercase mb-4">Login</button>
                </form>
                
                <div class="text-center mt-4">
                    <p class="small text-muted mb-0">Don't have an account? <a href="register.php" class="text-primary fw-bold text-decoration-none">Register Now</a></p>
                </div>
            </div>
            
            <div class="text-center mt-5">
                <a href="index.php" class="text-muted text-decoration-none small"><i class="fas fa-arrow-left me-1"></i> Back to Home</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
