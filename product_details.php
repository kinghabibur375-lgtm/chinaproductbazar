<?php
require 'config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: shop.php");
    exit;
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: shop.php");
    exit;
}

$page_title = $product['title'];
include 'header.php';

$img = !empty($product['image']) ? 'uploads/'.$product['image'] : 'uploads/no-img.jpg';
?>

<div class="details-container">
    
    <div style="margin-bottom: 20px; font-size: 0.9rem; color: var(--text-muted);">
        <a href="shop.php" style="color:var(--text-muted);">Shop</a> <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem; margin: 0 5px;"></i> 
        <span style="color:var(--primary); font-weight:600;">Product #<?= $product['id'] ?></span>
    </div>

    <div class="details-grid">
        <div class="details-gallery">
            <img src="<?= $img ?>" class="main-img" alt="<?= htmlspecialchars($product['title']) ?>">
        </div>

        <div class="details-info">
            <h1 style="margin-top: 0;"><?= htmlspecialchars($product['title']) ?></h1>
            <div class="rating">
                <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star-half-stroke"></i>
                <span style="color: var(--text-muted); margin-left: 10px;">(4.8/5.0 Customer Rating)</span>
            </div>
            <div class="price-tag">৳ <?= number_format($product['price_bdt']) ?></div>
            <div class="stock-badge"><i class="fa-solid fa-check-circle"></i> In Stock</div>

            <form action="cart_action.php" method="POST">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <div style="margin-bottom: 10px; font-weight: 600; font-size: 0.9rem;">Quantity</div>
                <div class="qty-selector">
                    <button type="button" class="qty-btn" onclick="this.nextElementSibling.stepDown()">-</button>
                    <input type="number" name="qty" class="qty-input" value="1" min="1" max="10" readonly>
                    <button type="button" class="qty-btn" onclick="this.previousElementSibling.stepUp()">+</button>
                </div>
                <button type="submit" name="buy_now" class="btn-primary" style="margin-top: 20px; font-size: 1.2rem; padding: 15px;">
                    Buy Now <i class="fa-solid fa-bolt"></i>
                </button>
            </form>

            <div style="margin-top: 30px; border-top: 1px solid var(--border); padding-top: 20px;">
                <h3 style="font-size: 1.1rem; margin-bottom: 10px;">Description</h3>
                <p style="color: var(--text-muted); line-height: 1.6; font-size: 0.95rem;">
                    <?= nl2br(htmlspecialchars($product['description'] ?? 'No description available.')) ?>
                </p>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>