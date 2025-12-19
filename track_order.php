<?php
require 'config.php';
$page_title = "Track Order | " . ($siteSettings['site_name'] ?? 'Store');
include 'header.php';

// Mock Logic
$status_result = null;
if (isset($_GET['order_id']) && !empty($_GET['order_id'])) {
    $status_result = [
        'id' => htmlspecialchars($_GET['order_id']),
        'status' => 'Processing',
        'msg' => 'Your order has been confirmed and is being packed.',
        'date' => date('d M Y')
    ];
}
?>

<div class="track-container">
    <div class="track-box">
        <div style="margin-bottom: 30px;">
            <i class="fa-solid fa-box-open" style="font-size: 3rem; color: var(--primary);"></i>
        </div>
        
        <h1 class="track-title">Track Your Order</h1>
        
        <?php if($status_result): ?>
            <div style="background:rgba(0,229,255,0.1); padding:25px; border-radius:12px; border:1px solid var(--primary); margin-bottom:30px;">
                <h2 style="color:var(--primary); margin:0 0 10px 0;">Status: <?= $status_result['status'] ?></h2>
                <div style="font-size:1.1rem; font-weight:bold; margin-bottom:5px;">Order #<?= $status_result['id'] ?></div>
                <p style="color:var(--text-muted); margin:0;"><?= $status_result['msg'] ?></p>
                <div style="margin-top:15px; font-size:0.8rem; color:var(--text-muted);">Last Updated: <?= $status_result['date'] ?></div>
                <a href="track_order.php" class="btn-primary" style="margin-top:20px; width:auto; display:inline-flex; padding:10px 25px;">Track Another</a>
            </div>
        <?php else: ?>
            <p class="track-desc">Enter your Order ID (sent via email) and Phone Number to check the current status of your delivery.</p>
            <form action="" method="GET">
                <div class="form-group" style="text-align:left;">
                    <label class="form-label">Order ID</label>
                    <input type="text" name="order_id" class="form-control" placeholder="e.g. ORD-8A9C" required>
                </div>
                <div class="form-group" style="text-align:left;">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" name="phone" class="form-control" placeholder="017xxxxxxxx" required>
                </div>
                <button type="submit" class="btn-primary">
                    Track Order <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>
<?php include 'footer.php'; ?>