<?php
// Iphilo Fragrance Admin Login Page
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already logged in
if (is_admin_logged_in()) {
    redirect('dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!verify_csrf_token($csrf_token)) {
        $error = "CSRF verification failed. Please try again.";
    } elseif (login_admin($username, $password)) {
        redirect('dashboard.php');
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Iphilo Fragrance</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background-color: #f8f9fa; }
        .login-card { max-width: 400px; margin: 100px auto; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card shadow-lg border-0 p-5 login-card">
            <div class="text-center mb-5">
                <img src="../assets/images/logo.png" alt="Iphilo Fragrance" height="50" class="mb-3">
                <h4 class="fw-bold text-uppercase">Admin Portal</h4>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger py-2 px-3 small rounded-0 mb-4"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form action="login.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <div class="mb-4">
                    <label for="username" class="form-label small fw-bold">Username</label>
                    <input type="text" name="username" id="username" class="form-control" required autofocus>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label small fw-bold">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-dark w-100 py-3 fw-bold text-uppercase">Login</button>
            </form>
            
            <div class="text-center mt-4">
                <a href="../index.php" class="text-muted text-decoration-none small"><i class="fas fa-arrow-left me-1"></i> Back to Website</a>
            </div>
        </div>
    </div>
</body>
</html>
