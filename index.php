<?php 
require 'config.php'; 
$page_title = "Home | " . ($siteSettings['site_name'] ?? 'Store');
include 'header.php'; 
?>

    <section class="hero-section">
        <div class="hero-content">
            <h2>Next Gen Tech</h2>
            <h1>Upgrade Your<br><span style="color:var(--primary)">Lifestyle</span> Today</h1>
            <p>Discover the latest gadgets and accessories imported directly for you. Premium quality, best prices.</p>
            <a href="shop.php" class="btn-primary" style="display:inline-block; width:auto; padding:15px 40px; text-decoration:none;">Shop Now <i class="fa-solid fa-arrow-right"></i></a>
        </div>
    </section>

    <section class="features-bar">
        <div class="feature-item">
            <i class="fa-solid fa-truck-fast feature-icon"></i>
            <div class="feature-text">
                <h4>Fast Delivery</h4>
                <p>2-3 Days Nationwide</p>
            </div>
        </div>
        <div class="feature-item">
            <i class="fa-solid fa-shield-halved feature-icon"></i>
            <div class="feature-text">
                <h4>Secure Payment</h4>
                <p>100% Secure Checkout</p>
            </div>
        </div>
        <div class="feature-item">
            <i class="fa-solid fa-rotate-left feature-icon"></i>
            <div class="feature-text">
                <h4>Easy Returns</h4>
                <p>7 Days Replacement</p>
            </div>
        </div>
        <div class="feature-item">
            <i class="fa-solid fa-headset feature-icon"></i>
            <div class="feature-text">
                <h4>24/7 Support</h4>
                <p>Always here for you</p>
            </div>
        </div>
    </section>

    <div class="container">
        
        <div class="section-header">
            <h2 class="section-title">Shop by Category</h2>
        </div>
        <div class="cat-grid">
            <a href="shop.php" class="cat-card"><i class="fa-solid fa-headphones cat-icon"></i><div class="cat-name">Audio</div></a>
            <a href="shop.php" class="cat-card"><i class="fa-solid fa-clock cat-icon"></i><div class="cat-name">Watch</div></a>
            <a href="shop.php" class="cat-card"><i class="fa-solid fa-mobile-screen cat-icon"></i><div class="cat-name">Mobile</div></a>
            <a href="shop.php" class="cat-card"><i class="fa-solid fa-laptop cat-icon"></i><div class="cat-name">Computer</div></a>
            <a href="shop.php" class="cat-card"><i class="fa-solid fa-camera cat-icon"></i><div class="cat-name">Camera</div></a>
            <a href="shop.php" class="cat-card"><i class="fa-solid fa-gamepad cat-icon"></i><div class="cat-name">Gaming</div></a>
        </div>

        <div class="section-header">
            <h2 class="section-title">Trending Products</h2>
            <a href="shop.php" class="view-all-btn">View All <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <div class="product-grid">
            <?php
            try {
                $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 8");
                while ($row = $stmt->fetch()):
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
            <?php endwhile; } catch (Exception $e) {} ?>
        </div>

        <div class="promo-banner">
            <div class="promo-content">
                <span class="promo-tag">LIMITED TIME OFFER</span>
                <h2 class="promo-title">Flash Sale <br>Up to 50% Off</h2>
                <p class="promo-desc">Grab your favorite gadgets at unbeatable prices. Offer valid until stocks last!</p>
                <a href="shop.php" class="btn-primary" style="display:inline-block; width:auto; padding:12px 35px; text-decoration:none;">Shop Sale</a>
            </div>
            <i class="fa-solid fa-bolt promo-bg-icon"></i>
        </div>

        <div class="section-header">
            <h2 class="section-title">New Arrivals</h2>
            <a href="shop.php" class="view-all-btn">View All <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <div class="product-grid">
            <?php
            try {
                $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 4 OFFSET 8");
                while ($row = $stmt->fetch()):
                    $img = !empty($row['image']) ? 'uploads/'.$row['image'] : 'uploads/no-img.jpg';
            ?>
            <div class="product-card">
                <a href="product_details.php?id=<?= $row['id'] ?>" class="img-wrap">
                    <img src="<?= $img ?>" class="product-img" alt="<?= htmlspecialchars($row['title']) ?>">
                </a>
                <div class="product-info">
                    <div class="product-cat">New</div>
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
            <?php endwhile; } catch (Exception $e) {} ?>
        </div>

        <div class="trust-grid">
            <div class="trust-item"><i class="fa-solid fa-award fa-2x" style="color:var(--primary)"></i><h4>Original Products</h4><p>100% Authentic Guarantee</p></div>
            <div class="trust-item"><i class="fa-solid fa-hand-holding-dollar fa-2x" style="color:var(--primary)"></i><h4>Cash On Delivery</h4><p>Pay upon receiving</p></div>
            <div class="trust-item"><i class="fa-solid fa-headset fa-2x" style="color:var(--primary)"></i><h4>24/7 Support</h4><p>We are here to help</p></div>
            <div class="trust-item"><i class="fa-solid fa-percent fa-2x" style="color:var(--primary)"></i><h4>Best Price</h4><p>Guaranteed low prices</p></div>
        </div>

    </div>

<?php include 'footer.php'; ?>