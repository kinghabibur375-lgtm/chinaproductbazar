<?php
session_start();
require 'config.php';

if (!isset($_POST['place_order']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

$order_id = "ORD-" . strtoupper(substr(md5(uniqid()), 0, 8));
$order_date = date("d M Y, h:i A");
$name = htmlspecialchars($_POST['name']);
$phone = htmlspecialchars($_POST['phone']);
$address = nl2br(htmlspecialchars($_POST['address']));

$cart_ids = implode(',', array_keys($_SESSION['cart']));
$cart_products = [];
$total_price = 0;
$delivery_charge = 100;

if ($cart_ids) {
    $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($cart_ids)");
    while ($row = $stmt->fetch()) {
        $row['qty'] = $_SESSION['cart'][$row['id']];
        $row['subtotal'] = $row['price_bdt'] * $row['qty'];
        $total_price += $row['subtotal'];
        $cart_products[] = $row;
    }
}
$grand_total = $total_price + $delivery_charge;

$page_title = "Order Confirmed";
include 'header.php';
?>

<div class="invoice-container">
    
    <div style="text-align: center; margin-bottom: 30px;" class="success-banner">
        <div style="width: 70px; height: 70px; background: #00c853; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 35px; color: white; margin: 0 auto 15px auto; box-shadow: 0 5px 15px rgba(0,200,83,0.3);">
            <i class="fa-solid fa-check"></i>
        </div>
        <h1 style="font-family: 'Rajdhani'; font-weight:800; margin-bottom:5px;">Order Placed Successfully!</h1>
        <p style="color: var(--text-muted);">Thank you, <?= $name ?>. Here is your order receipt.</p>
    </div>

    <div class="invoice-card">
        
        <div class="print-only-header">
            <div class="print-logo">CHINA PRODUCT BAZAR</div>
            <div class="print-sub">Dhaka, Bangladesh | +880 1874648952 | support@chinaproductbazar.com</div>
        </div>

        <div class="invoice-header-new">
            <div class="invoice-brand-new">CHINA <span>Product Bazar</span></div>
            <div class="invoice-badge">INVOICE RECEIPT</div>
        </div>

        <div class="order-summary-bar">
            <div class="order-summ-item">
                <label>Order ID</label>
                <span><?= $order_id ?></span>
            </div>
            <div class="order-summ-item">
                <label>Order Date</label>
                <span><?= $order_date ?></span>
            </div>
            <div class="order-summ-item">
                <label>Payment Method</label>
                <span style="color:#00c853;"><i class="fa-solid fa-hand-holding-dollar"></i> Cash on Delivery</span>
            </div>
        </div>

        <div class="invoice-content-pad">
            <div class="customer-split">
                <div class="cust-col">
                    <h4><i class="fa-solid fa-user-tie"></i> Billed To:</h4>
                    <p style="font-weight:700; font-size:1.1rem;"><?= $name ?></p>
                    <p><?= $phone ?></p>
                    <p>Dhaka, Bangladesh</p>
                </div>
                <div class="cust-col">
                    <h4><i class="fa-solid fa-truck-fast"></i> Shipped To:</h4>
                    <p><?= $address ?></p>
                </div>
            </div>

            <div class="invoice-table-container">
                <table class="invoice-table-new">
                    <thead>
                        <tr>
                            <th>Item Description</th>
                            <th style="text-align:center;">Qty</th>
                            <th style="text-align:right;">Unit Price</th>
                            <th style="text-align:right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_products as $item): 
                             $img = !empty($item['image']) ? 'uploads/'.$item['image'] : 'uploads/no-img.jpg';
                        ?>
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <img src="<?= $img ?>" class="summary-img" style="width:40px; height:40px; border-radius:6px;">
                                    <div>
                                        <div style="font-weight:600;"><?= htmlspecialchars($item['title']) ?></div>
                                        <div style="font-size:0.85rem; color:var(--text-muted);">Item ID: #<?= $item['id'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align:center; font-weight:600;"><?= $item['qty'] ?></td>
                            <td style="text-align:right;">৳ <?= number_format($item['price_bdt']) ?></td>
                            <td style="text-align:right; font-weight:700;">৳ <?= number_format($item['subtotal']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="invoice-footer-new">
                <div class="totals-box-new">
                    <div class="total-row-new" style="display:flex; justify-content:space-between; margin-bottom:10px;">
                        <span style="color:var(--text-muted);">Subtotal:</span>
                        <span style="font-weight:700;">৳ <?= number_format($total_price) ?></span>
                    </div>
                    <div class="total-row-new" style="display:flex; justify-content:space-between; margin-bottom:10px;">
                        <span style="color:var(--text-muted);">Delivery Charge:</span>
                        <span style="font-weight:700;">৳ <?= number_format($delivery_charge) ?></span>
                    </div>
                    <div class="grand-total-new">
                        <span>GRAND TOTAL</span>
                        <span>৳ <?= number_format($grand_total) ?></span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="action-buttons-new">
        <button onclick="window.print()" class="btn-print-new">
            <i class="fa-solid fa-print"></i> Print Receipt
        </button>
        <a href="shop.php" class="btn-primary" style="width:auto; padding: 12px 30px; text-decoration:none; margin-top:0;">
            Continue Shopping
        </a>
    </div>

</div>

<?php 
unset($_SESSION['cart']);
include 'footer.php'; 
?>