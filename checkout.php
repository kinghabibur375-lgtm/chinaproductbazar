<?php
require 'config.php';
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) { header("Location: index.php"); exit; }
$page_title = "Checkout";
include 'header.php';
$total_price = 0; $delivery_charge = 100; $cart_items = [];
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
    while ($row = $stmt->fetch()) {
        $row['qty'] = $_SESSION['cart'][$row['id']];
        $row['subtotal'] = $row['price_bdt'] * $row['qty'];
        $total_price += $row['subtotal'];
        $cart_items[] = $row;
    }
}
$grand_total = $total_price + $delivery_charge;
?>
<div class="checkout-container">
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 style="font-family: 'Rajdhani'; margin-bottom: 10px;">Secure Checkout</h1>
        <p style="color: #888;">Complete your order details below.</p>
    </div>
    <form action="place_order.php" method="POST" class="checkout-grid">
        <div class="checkout-card">
            <h3 class="checkout-title"><i class="fa-regular fa-address-card"></i> Billing Details</h3>
            <div class="form-group"><label class="form-label">Full Name</label><input type="text" name="name" class="form-control" required></div>
            <div class="form-group"><label class="form-label">Phone</label><input type="tel" name="phone" class="form-control" required></div>
            <div class="form-group"><label class="form-label">Address</label><textarea name="address" class="form-control" required style="height: 100px;"></textarea></div>
            <div class="form-group"><label class="form-label">Note</label><textarea name="note" class="form-control"></textarea></div>
        </div>
        <div class="checkout-card">
            <h3 class="checkout-title"><i class="fa-solid fa-cart-flatbed"></i> Order Summary</h3>
            <div>
                <?php foreach ($cart_items as $item): 
                    $img = !empty($item['image']) ? 'uploads/'.$item['image'] : 'uploads/no-img.jpg';
                ?>
                <div class="summary-item">
                    <img src="<?= $img ?>" class="summary-img">
                    <div class="summary-info"><div class="summary-title"><?= htmlspecialchars($item['title']) ?></div><div class="summary-meta">Qty: <?= $item['qty'] ?></div></div>
                    <div class="summary-price">৳ <?= number_format($item['subtotal']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="summary-total"><span>Grand Total</span><span style="color:var(--primary); font-family:'Rajdhani'; font-size:1.5rem;">৳ <?= number_format($grand_total) ?></span></div>
            <button type="submit" name="place_order" class="btn-place-order">Confirm Order <i class="fa-solid fa-check"></i></button>
        </div>
    </form>
</div>
<?php include 'footer.php'; ?>