<?php
// Iphilo Fragrance Authentication Functions
require_once __DIR__ . '/db.php';

/**
 * Customer Authentication
 */
function is_customer_logged_in() {
    return isset($_SESSION['customer_id']);
}

function login_customer($email, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, email, password, first_name, last_name FROM customers WHERE email = ?");
    $stmt->execute([$email]);
    $customer = $stmt->fetch();

    if ($customer && password_verify($password, $customer['password'])) {
        $_SESSION['customer_id'] = $customer['id'];
        $_SESSION['customer_email'] = $customer['email'];
        $_SESSION['customer_name'] = $customer['first_name'] . ' ' . $customer['last_name'];
        return true;
    }
    return false;
}

function logout_customer() {
    unset($_SESSION['customer_id']);
    unset($_SESSION['customer_email']);
    unset($_SESSION['customer_name']);
    session_destroy();
}

/**
 * Admin Authentication
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin_id']);
}

function login_admin($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, username, password, full_name, role FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_name'] = $admin['full_name'];
        $_SESSION['admin_role'] = $admin['role'];
        return true;
    }
    return false;
}

function logout_admin() {
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_username']);
    unset($_SESSION['admin_name']);
    unset($_SESSION['admin_role']);
}

/**
 * CSRF Protection
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
