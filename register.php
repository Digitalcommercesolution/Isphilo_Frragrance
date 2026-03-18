<?php
// Iphilo Fragrance Register Page
$page_title = "Create Account";
require_once __DIR__ . '/includes/header.php';

// Redirect if already logged in
if (is_customer_logged_in()) {
    redirect('customer-dashboard.php');
}

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = sanitize($_POST['first_name']);
    $last_name = sanitize($_POST['last_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Simple validation
    if (!verify_csrf_token($csrf_token)) {
        $error = "CSRF verification failed. Please try again.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM customers WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email address already exists.";
        } else {
            // Insert new customer
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO customers (first_name, last_name, email, phone, password) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$first_name, $last_name, $email, $phone, $hashed_password])) {
                $success = "Registration successful! Please login to your account.";
            } else {
                $error = "An error occurred during registration. Please try again.";
            }
        }
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-10">
            <div class="card shadow-lg border-0 p-5 bg-white">
                <div class="text-center mb-5">
                    <h2 class="fw-bold text-uppercase mb-2">Create Account</h2>
                    <p class="text-muted small">Join Iphilo Fragrance for a premium experience.</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger py-2 px-3 small rounded-0 mb-4"><?php echo $error; ?></div>
                <?php elseif ($success): ?>
                    <div class="alert alert-success py-2 px-3 small rounded-0 mb-4"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form action="register.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label small fw-bold">First Name</label>
                            <input type="text" name="first_name" id="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label small fw-bold">Last Name</label>
                            <input type="text" name="last_name" id="last_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="email" class="form-label small fw-bold">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label for="phone" class="form-label small fw-bold">Phone Number</label>
                        <input type="tel" name="phone" id="phone" class="form-control" required>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="password" class="form-label small fw-bold">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="confirm_password" class="form-label small fw-bold">Confirm Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="terms" required>
                        <label class="form-check-label small text-muted" for="terms">I agree to the <a href="terms-conditions.php" class="text-primary text-decoration-none fw-bold">Terms & Conditions</a></label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold text-uppercase mb-4">Register</button>
                </form>
                
                <div class="text-center mt-4">
                    <p class="small text-muted mb-0">Already have an account? <a href="login.php" class="text-primary fw-bold text-decoration-none">Login Now</a></p>
                </div>
            </div>
            
            <div class="text-center mt-5">
                <a href="index.php" class="text-muted text-decoration-none small"><i class="fas fa-arrow-left me-1"></i> Back to Home</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
