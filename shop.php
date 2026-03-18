<?php
// Iphilo Fragrance Shop Page
$page_title = "Shop Fragrances";
require_once __DIR__ . '/includes/header.php';

// Pagination settings
$limit = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filter and Sort Parameters
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Base Query
$query = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_active = 1";
$params = [];

if ($category_id > 0) {
    $query .= " AND p.category_id = ?";
    $params[] = $category_id;
}

if (!empty($search)) {
    $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Total Count for Pagination
$count_stmt = $pdo->prepare($query);
$count_stmt->execute($params);
$total_items = $count_stmt->rowCount();
$total_pages = ceil($total_items / $limit);

// Sorting
switch ($sort) {
    case 'price_low':
        $query .= " ORDER BY p.price ASC";
        break;
    case 'price_high':
        $query .= " ORDER BY p.price DESC";
        break;
    case 'popular':
        $query .= " ORDER BY p.is_bestseller DESC, p.created_at DESC";
        break;
    default:
        $query .= " ORDER BY p.created_at DESC";
        break;
}

// Limit and Offset
$query .= " LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = get_all_categories();
?>

<div class="container py-5">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-5">
            <div class="card shadow-sm border-0 mb-4 p-4 bg-light">
                <h5 class="fw-bold mb-4">Search</h5>
                <form action="shop.php" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?php echo sanitize($search); ?>">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                    <?php if ($category_id > 0): ?><input type="hidden" name="category" value="<?php echo $category_id; ?>"><?php endif; ?>
                    <?php if ($sort != 'newest'): ?><input type="hidden" name="sort" value="<?php echo sanitize($sort); ?>"><?php endif; ?>
                </form>
            </div>
            
            <div class="card shadow-sm border-0 mb-4 p-4 bg-light">
                <h5 class="fw-bold mb-4">Categories</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="shop.php?category=0" class="text-decoration-none <?php echo $category_id == 0 ? 'text-primary fw-bold' : 'text-muted'; ?>">All Fragrances</a></li>
                    <?php foreach ($categories as $cat): ?>
                        <li class="mb-2"><a href="shop.php?category=<?php echo $cat['id']; ?>" class="text-decoration-none <?php echo $category_id == $cat['id'] ? 'text-primary fw-bold' : 'text-muted'; ?>"><?php echo $cat['name']; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="card shadow-sm border-0 p-4 bg-light">
                <h5 class="fw-bold mb-4">Sort By</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="shop.php?sort=newest&category=<?php echo $category_id; ?>&search=<?php echo sanitize($search); ?>" class="text-decoration-none <?php echo $sort == 'newest' ? 'text-primary fw-bold' : 'text-muted'; ?>">Newest Arrivals</a></li>
                    <li class="mb-2"><a href="shop.php?sort=popular&category=<?php echo $category_id; ?>&search=<?php echo sanitize($search); ?>" class="text-decoration-none <?php echo $sort == 'popular' ? 'text-primary fw-bold' : 'text-muted'; ?>">Popularity</a></li>
                    <li class="mb-2"><a href="shop.php?sort=price_low&category=<?php echo $category_id; ?>&search=<?php echo sanitize($search); ?>" class="text-decoration-none <?php echo $sort == 'price_low' ? 'text-primary fw-bold' : 'text-muted'; ?>">Price: Low to High</a></li>
                    <li class="mb-2"><a href="shop.php?sort=price_high&category=<?php echo $category_id; ?>&search=<?php echo sanitize($search); ?>" class="text-decoration-none <?php echo $sort == 'price_high' ? 'text-primary fw-bold' : 'text-muted'; ?>">Price: High to Low</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Product Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <p class="mb-0 text-muted">Showing <?php echo count($products); ?> of <?php echo $total_items; ?> products</p>
            </div>
            
            <?php if (count($products) > 0): ?>
                <div class="row">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-4 mb-4">
                            <div class="product-card shadow-sm h-100">
                                <?php if ($product['is_bestseller']): ?><span class="product-badge bg-primary">Bestseller</span><?php endif; ?>
                                <?php if ($product['sale_price']): ?><span class="product-badge bg-danger">Sale</span><?php endif; ?>
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
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-5">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="shop.php?page=<?php echo $page - 1; ?>&category=<?php echo $category_id; ?>&sort=<?php echo $sort; ?>&search=<?php echo sanitize($search); ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                    <a class="page-link" href="shop.php?page=<?php echo $i; ?>&category=<?php echo $category_id; ?>&sort=<?php echo $sort; ?>&search=<?php echo sanitize($search); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="shop.php?page=<?php echo $page + 1; ?>&category=<?php echo $category_id; ?>&sort=<?php echo $sort; ?>&search=<?php echo sanitize($search); ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-search-minus fs-1 text-muted mb-3"></i>
                    <h3>No products found</h3>
                    <p class="text-muted">Try adjusting your filters or search terms.</p>
                    <a href="shop.php" class="btn btn-primary mt-3">Reset All Filters</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
