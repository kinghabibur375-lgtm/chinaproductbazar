<?php
error_reporting(0);
require 'config.php';
$page_title = "Shop | " . ($siteSettings['site_name'] ?? 'Store');
include 'header.php';

try {
    // --- FILTER LOGIC (No Category) ---
    $where = "1=1";
    $params = [];

    $min_price = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? $_GET['min_price'] : 0;
    $max_price = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? $_GET['max_price'] : 100000;
    $where .= " AND price_bdt BETWEEN ? AND ?";
    $params[] = $min_price;
    $params[] = $max_price;

    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
    $order_by = "id DESC"; 
    if ($sort == 'price_low') $order_by = "price_bdt ASC";
    if ($sort == 'price_high') $order_by = "price_bdt DESC";

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $per_page = 12;
    $offset = ($page - 1) * $per_page;

    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE $where");
    $count_stmt->execute($params);
    $total_products = $count_stmt->fetchColumn();
    $total_pages = ceil($total_products / $per_page);

    $sql = "SELECT * FROM products WHERE $where ORDER BY $order_by LIMIT $offset, $per_page";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();

} catch (PDOException $e) {
    $products = [];
}
?>

<div class="shop-container">
    <div class="mobile-filter-btn" onclick="document.querySelector('.shop-sidebar').classList.toggle('active')">
        <i class="fa-solid fa-filter"></i> Filter Price
    </div>

    <div class="shop-layout">
        <aside class="shop-sidebar">
            <form action="shop.php" method="GET">
                <div class="filter-group">
                    <div class="filter-title">Price Range (৳)</div>
                    <div class="price-inputs">
                        <input type="number" name="min_price" class="price-input" placeholder="Min" value="<?= $min_price ?>">
                        <input type="number" name="max_price" class="price-input" placeholder="Max" value="<?= $max_price ?>">
                    </div>
                    <button type="submit" class="btn-filter">Apply Filter</button>
                </div>
            </form>
        </aside>

        <main class="shop-content">
            <div class="shop-toolbar">
                <div class="product-count">Showing <strong><?= count($products) ?></strong> of <strong><?= $total_products ?></strong> results</div>
                <form action="shop.php" method="GET" id="sortForm">
                    <input type="hidden" name="min_price" value="<?= $min_price ?>">
                    <input type="hidden" name="max_price" value="<?= $max_price ?>">
                    <select name="sort" class="sort-select" onchange="document.getElementById('sortForm').submit()">
                        <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>Newest First</option>
                        <option value="price_low" <?= $sort == 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                        <option value="price_high" <?= $sort == 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                    </select>
                </form>
            </div>

            <?php if(count($products) > 0): ?>
                <div class="product-grid">
                    <?php foreach($products as $row): 
                        $img = !empty($row['image']) ? 'uploads/'.$row['image'] : 'uploads/no-img.jpg';
                    ?>
                    <div class="product-card">
                        <a href="product_details.php?id=<?= $row['id'] ?>" class="img-wrap">
                            <img src="<?= $img ?>" class="product-img" alt="<?= htmlspecialchars($row['title']) ?>">
                        </a>
                        <div class="product-info">
                            <div class="product-cat">Gadget</div>
                            <a href="product_details.php?id=<?= $row['id'] ?>" class="product-title">
                                <?= htmlspecialchars($row['title']) ?>
                            </a>
                            <div class="product-price">৳ <?= number_format($row['price_bdt']) ?></div>
                            <form action="cart_action.php" method="POST" style="margin-top: auto;">
                                <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="buy_now" class="btn-primary" style="padding:10px; font-size:0.9rem;">
                                    Buy Now <i class="fa-solid fa-bolt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="shop.php?page=<?= $i ?>&sort=<?= $sort ?>" class="page-link <?= $page == $i ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>

            <?php else: ?>
                <div style="text-align:center; padding:50px;">
                    <h3>No products found.</h3>
                    <a href="shop.php" class="btn-primary" style="display:inline-block; width:auto; padding:10px 30px;">Reset</a>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>
<?php include 'footer.php'; ?>