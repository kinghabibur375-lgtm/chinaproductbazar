<?php
require 'config.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$order_id = $_GET['id'];

// Fetch Order Details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    echo "Invalid Order ID"; exit;
}

// Fetch Items
$stmt_items = $pdo->prepare("SELECT oi.*, p.title, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE order_id = ?");
$stmt_items->execute([$order_id]);
$items = $stmt_items->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Invoice #<?= $order_id ?></title>
    <link rel="stylesheet" href="style.css?v=7.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* SCREEN STYLES (Your Dark Theme) */
        .success-card { text-align: center; padding: 40px 20px; background: var(--bg-card); border-radius: 15px; border: 1px solid var(--primary); margin-bottom: 30px; }
        .check-icon { font-size: 4rem; color: #00e676; margin-bottom: 20px; animation: bounce 1s infinite alternate; }
        @keyframes bounce { from { transform: translateY(0); } to { transform: translateY(-10px); } }
        
        .order-details-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; text-align: left; }
        .detail-box { background: #0b0f19; padding: 20px; border-radius: 10px; border: 1px solid var(--border); }
        .item-row { display: flex; gap: 15px; align-items: center; border-bottom: 1px solid var(--border); padding: 10px 0; }
        .item-row:last-child { border-bottom: none; }
        .item-img { width: 60px; height: 60px; object-fit: cover; border-radius: 5px; }
        
        /* HIDDEN PRINT HEADER (Only shows on paper) */
        .print-only-header { display: none; }

        @media (max-width: 768px) { .order-details-grid { grid-template-columns: 1fr; } }

        /* ================= PRINT STYLES ================= */
        @media print {
            .main-header, .success-card, .hero-btn, footer, .check-icon, .no-print { display: none !important; }
            body { background: white !important; color: black !important; font-family: 'Helvetica', sans-serif; margin: 0; padding: 0; }
            .print-only-header { display: flex !important; justify-content: space-between; align-items: center; border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 30px; }
            .print-logo { height: 60px; }
            .print-title { font-size: 24px; font-weight: bold; text-transform: uppercase; }
            .container { padding: 20px !important; max-width: 100% !important; margin: 0 !important; }
            .order-details-grid { display: block !important; } 
            .detail-box { background: white !important; border: 1px solid #ddd !important; color: black !important; margin-bottom: 20px; page-break-inside: avoid; }
            h3, label, div, span, small { color: black !important; }
            .item-row { border-bottom: 1px solid #eee !important; }
            a { text-decoration: none; color: black; }
        }
    </style>
</head>
<body>

    <header class="main-header no-print">
        <a href="index.php" class="logo">
            <?php if(file_exists('uploads/logo.png')): ?>
                <img src="uploads/logo.png" alt="China Product Bazar" class="logo-img">
            <?php else: ?>
                <div class="logo-text">CHINA <span>Product Bazar</span></div>
            <?php endif; ?>
        </a>
    </header>

    <div class="container" style="padding-top: 3rem;">
        
        <div class="print-only-header">
            <div>
                <?php if(file_exists('uploads/logo.png')): ?>
                    <img src="uploads/logo.png" class="print-logo" alt="Logo">
                <?php else: ?>
                    <h2>CHINA PRODUCT BAZAR</h2>
                <?php endif; ?>
                <div style="font-size: 12px; margin-top: 5px;">support@chinaproductbazar.com | +880 1874648952</div>
            </div>
            <div style="text-align: right;">
                <div class="print-title">INVOICE</div>
                <div>Order #<?= $order_id ?></div>
                <div>Date: <?= date('d M Y') ?></div>
            </div>
        </div>

        <div class="success-card no-print">
            <i class="fa-solid fa-circle-check check-icon"></i>
            <h1 style="color:white; margin:0;">Thank You for Your Order!</h1>
            <p style="color:var(--text-gray); font-size: 1.1rem; margin-top: 10px;">Your order has been placed successfully.</p>
            <div style="margin-top: 20px; font-size: 1.2rem; color: var(--primary); font-weight: bold;">
                Order ID: #<?= $order_id ?>
            </div>
            <p style="color:#00e676; margin-top:5px;"><i class="fa-solid fa-envelope"></i> We have received your order and will call you shortly for confirmation.</p>
        </div>

        <div class="order-details-grid">
            
            <div class="detail-box">
                <h3 style="color:white; border-bottom: 1px solid var(--border); padding-bottom: 10px; margin-top:0;">Order Items</h3>
                <?php foreach($items as $item): ?>
                <div class="item-row">
                    <img src="uploads/<?= $item['image'] ?>" class="item-img">
                    <div style="flex:1;">
                        <div style="color:white; font-weight:600;"><?= htmlspecialchars($item['title']) ?></div>
                        <small style="color:var(--text-gray);">Qty: <?= $item['quantity'] ?></small>
                    </div>
                    <div style="color:var(--primary); font-weight:bold;">৳ <?= number_format($item['price'] * $item['quantity']) ?></div>
                </div>
                <?php endforeach; ?>
                
                <div style="margin-top: 20px; display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: bold; color: white; border-top: 1px solid var(--border); padding-top: 15px;">
                    <span>Total Amount</span>
                    <span>৳ <?= number_format($order['total_amount']) ?></span>
                </div>
            </div>

            <div class="detail-box">
                <h3 style="color:white; border-bottom: 1px solid var(--border); padding-bottom: 10px; margin-top:0;">Delivery Details</h3>
                
                <div style="margin-bottom: 15px;">
                    <label style="color:var(--text-gray); font-size:0.9rem;">Customer Name</label>
                    <div style="color:white; font-weight:600;"><?= htmlspecialchars($order['customer_name']) ?></div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="color:var(--text-gray); font-size:0.9rem;">Phone Number</label>
                    <div style="color:white; font-weight:600;"><?= htmlspecialchars($order['customer_phone']) ?></div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="color:var(--text-gray); font-size:0.9rem;">Shipping Address</label>
                    <div style="color:white; font-weight:600;"><?= nl2br(htmlspecialchars($order['customer_address'])) ?></div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="color:var(--text-gray); font-size:0.9rem;">Payment Method</label>
                    <div style="color:var(--primary); font-weight:600;"><?= $order['payment_method'] ?></div>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="color:var(--text-gray); font-size:0.9rem;">Order Date</label>
                    <div style="color:white;"><?= date('d M Y, h:i A', strtotime($order['order_date'])) ?></div>
                </div>

                <button onclick="window.print()" class="buy-btn no-print" style="width:100%; margin-top: 10px; font-size:0.9rem;">
                    <i class="fa-solid fa-print"></i> Print Receipt
                </button>
            </div>

        </div>

        <div class="no-print" style="text-align:center; margin-top: 40px;">
            <a href="index.php" class="hero-btn"><i class="fa-solid fa-arrow-left"></i> Continue Shopping</a>
            <a href="track_order.php" class="hero-btn" style="background: transparent; border: 1px solid var(--primary); margin-left: 10px;">Track Order</a>
        </div>

    </div>

    <footer class="no-print" style="text-align:center; padding: 2rem; color: #718096; margin-top: 3rem;">
        &copy; <?= date('Y') ?> China Product Bazar. All rights reserved.
    </footer>

</body>
</html>