<?php
// Iphilo Fragrance Admin Products Management
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if not logged in
if (!is_admin_logged_in()) {
    redirect('login.php');
}

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch Categories for forms
$categories = get_all_categories();

// Fetch Products
$products = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management | Iphilo Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background-color: #f4f7f6; }
        .sidebar { min-height: 100vh; width: 250px; background: #2c3e50; color: white; position: fixed; }
        .sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 15px 25px; border-left: 4px solid transparent; transition: all 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: rgba(255,255,255,0.1); border-left-color: var(--primary-color); }
        .sidebar .nav-link i { width: 20px; margin-right: 10px; }
        .main-content { margin-left: 250px; padding: 40px; }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar d-none d-lg-block">
        <div class="p-4 text-center">
            <img src="../assets/images/logo.png" alt="Iphilo Logo" height="40" class="mb-2">
            <h5 class="fw-bold text-uppercase mb-0">Iphilo Admin</h5>
        </div>
        <nav class="nav flex-column mt-4">
            <a href="dashboard.php" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="products.php" class="nav-link active"><i class="fas fa-box"></i> Products</a>
            <a href="orders.php" class="nav-link"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="customers.php" class="nav-link"><i class="fas fa-users"></i> CRM</a>
            <a href="reviews.php" class="nav-link"><i class="fas fa-star"></i> Reviews</a>
            <a href="reports.php" class="nav-link"><i class="fas fa-chart-line"></i> Reports</a>
            <a href="settings.php" class="nav-link"><i class="fas fa-cog"></i> Settings</a>
            <a href="logout.php" class="nav-link text-danger mt-5"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold mb-1">Product Management</h2>
                <p class="text-muted small mb-0">Manage your fragrance catalog.</p>
            </div>
            <a href="products.php?action=add" class="btn btn-primary px-4 py-2 fw-bold text-uppercase"><i class="fas fa-plus me-2"></i> Add New Product</a>
        </header>

        <!-- Product List -->
        <div class="card border-0 shadow-sm p-4 bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="small text-muted text-uppercase">
                            <th scope="col" class="py-3">Product</th>
                            <th scope="col" class="py-3">Category</th>
                            <th scope="col" class="py-3">Price</th>
                            <th scope="col" class="py-3">Stock</th>
                            <th scope="col" class="py-3">Status</th>
                            <th scope="col" class="py-3 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td class="py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="product-img me-3 bg-light rounded shadow-sm" style="width: 50px; height: 50px; overflow: hidden;">
                                            <img src="../<?php echo get_product_image($product['id']); ?>" class="img-fluid w-100 h-100 object-fit-cover">
                                        </div>
                                        <div>
                                            <p class="small fw-bold mb-0"><?php echo $product['name']; ?></p>
                                            <p class="small text-muted mb-0">ID: #<?php echo $product['id']; ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 small"><?php echo $product['category_name']; ?></td>
                                <td class="py-3 fw-bold"><?php echo format_currency($product['price']); ?></td>
                                <td class="py-3">
                                    <span class="small fw-bold <?php echo $product['stock_quantity'] <= 5 ? 'text-danger' : 'text-muted'; ?>"><?php echo $product['stock_quantity']; ?></span>
                                </td>
                                <td class="py-3">
                                    <span class="badge rounded-pill bg-<?php echo $product['is_active'] ? 'success' : 'secondary'; ?> px-3"><?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?></span>
                                </td>
                                <td class="py-3 text-end">
                                    <a href="products.php?action=edit&id=<?php echo $product['id']; ?>" class="btn btn-link text-primary p-0 me-2"><i class="fas fa-edit"></i></a>
                                    <a href="products.php?action=delete&id=<?php echo $product['id']; ?>" class="btn btn-link text-danger p-0" onclick="return confirm('Are you sure you want to delete this product?')"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
