<?php
require 'config.php';
$page_title = "My Cart";
include 'header.php';

$total_price = 0; $delivery_charge = 100; $cart_empty = true; $stmt = null;
if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    $cart_empty = false;
    $ids = implode(',', array_keys($_SESSION['cart']));
    $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
}
?>

<div class="cart-container">
    <div style="margin-bottom: 30px; border-bottom: 1px solid var(--border); padding-bottom: 15px;">
        <h1 style="font-family: 'Rajdhani'; margin: 0;">Shopping Cart</h1>
    </div>

    <?php if ($cart_empty): ?>
        <div style="text-align: center; padding: 60px;">
            <i class="fa-solid fa-cart-arrow-down" style="font-size: 4rem; color: var(--text-muted); margin-bottom: 20px;"></i>
            <h2>Your cart is empty</h2>
            <a href="shop.php" class="btn-primary" style="display:inline-block; width:auto; padding: 12px 30px;">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="cart-grid">
            <div class="cart-table-wrapper">
                <table class="cart-table">
                    <thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Total</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php while ($row = $stmt->fetch()): 
                            $qty = $_SESSION['cart'][$row['id']];
                            $subtotal = $row['price_bdt'] * $qty; $total_price += $subtotal;
                            $img = !empty($row['image']) ? 'uploads/'.$row['image'] : 'uploads/no-img.jpg';
                        ?>
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:15px;">
                                    <img src="<?= $img ?>" class="cart-thumb">
                                    <div>
                                        <a href="product_details.php?id=<?= $row['id'] ?>" style="font-weight:600; display:block; margin-bottom:5px;"><?= htmlspecialchars($row['title']) ?></a>
                                        <span style="font-size:0.8rem; color:var(--text-muted);">ID: #<?= $row['id'] ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>৳ <?= number_format($row['price_bdt']) ?></td>
                            <td>
                                <div class="qty-mini-control">
                                    <a href="cart_action.php?action=decrease&id=<?= $row['id'] ?>" class="qty-btn">-</a>
                                    <div class="qty-input"><?= $qty ?></div>
                                    <a href="cart_action.php?action=increase&id=<?= $row['id'] ?>" class="qty-btn">+</a>
                                </div>
                            </td>
                            <td style="color:var(--primary); font-weight:bold;">৳ <?= number_format($subtotal) ?></td>
                            <td><a href="cart_action.php?remove=<?= $row['id'] ?>" style="color:#ff1744;"><i class="fa-solid fa-trash"></i></a></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="cart-summary">
                <h3 style="font-family:'Rajdhani'; margin-bottom:20px; border-bottom:1px solid var(--border); padding-bottom:10px;">Order Summary</h3>
                <div class="summary-item"><span>Subtotal</span><span>৳ <?= number_format($total_price) ?></span></div>
                <div class="summary-item"><span>Delivery Charge</span><span>৳ <?= number_format($delivery_charge) ?></span></div>
                <div class="summary-total"><span>Grand Total</span><span style="color:var(--primary);">৳ <?= number_format($total_price + $delivery_charge) ?></span></div>
                <form action="checkout.php" method="POST">
                    <button type="submit" class="btn-primary">Proceed to Checkout</button>
                </form>
                <a href="shop.php" style="display:block; text-align:center; margin-top:15px; font-size:0.9rem; color:var(--text-muted);"><i class="fa-solid fa-arrow-left"></i> Continue Shopping</a>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>