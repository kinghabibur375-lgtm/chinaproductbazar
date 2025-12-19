<?php
require 'config.php';
if (!isset($_SESSION['admin_logged_in'])) header("Location: admin_login.php");

// Get distinct customers from orders
$customers = $pdo->query("SELECT DISTINCT customer_name, customer_phone, customer_address, MAX(order_date) as last_order FROM orders GROUP BY customer_phone ORDER BY last_order DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Customers</title>
    <link rel="stylesheet" href="style.css?v=5.0">
    <style>body { background: #0b0f19; color: white; display: flex; }</style>
</head>
<body>
    <?php include 'admin_sidebar.php'; ?>
    <div style="flex:1; padding:30px;">
        <h2>Customer Database</h2>
        <table style="width:100%; border-collapse:collapse; background:#151a27; margin-top:20px;">
            <thead><tr><th style="padding:15px; text-align:left; border-bottom:1px solid #2a3b55;">Name</th><th style="padding:15px; text-align:left; border-bottom:1px solid #2a3b55;">Phone</th><th style="padding:15px; text-align:left; border-bottom:1px solid #2a3b55;">Address</th><th style="padding:15px; text-align:left; border-bottom:1px solid #2a3b55;">Last Active</th></tr></thead>
            <tbody>
                <?php foreach($customers as $c): ?>
                <tr>
                    <td style="padding:15px; border-bottom:1px solid #2a3b55;"><?= htmlspecialchars($c['customer_name']) ?></td>
                    <td style="padding:15px; border-bottom:1px solid #2a3b55;"><?= htmlspecialchars($c['customer_phone']) ?></td>
                    <td style="padding:15px; border-bottom:1px solid #2a3b55;"><?= htmlspecialchars($c['customer_address']) ?></td>
                    <td style="padding:15px; border-bottom:1px solid #2a3b55;"><?= date('d M Y', strtotime($c['last_order'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>