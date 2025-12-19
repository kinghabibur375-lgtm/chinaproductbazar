<?php
require 'config.php';
if (!isset($_SESSION['admin_logged_in'])) header("Location: admin_login.php");

$orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Orders | Admin</title>
    <link rel="stylesheet" href="style.css?v=5.0">
    <style>
        body { background: #0b0f19; color: white; font-family: sans-serif; display: flex; }
        .main { flex: 1; padding: 30px; }
        table { width: 100%; border-collapse: collapse; background: #151a27; border-radius: 8px; }
        th, td { padding: 15px; border-bottom: 1px solid #2a3b55; text-align: left; }
        .badge { padding: 5px 10px; border-radius: 4px; font-size: 0.8rem; }
        .Pending { background: #ffab00; color: black; }
        .Shipped { background: #00e5ff; color: black; }
        .Delivered { background: #00c853; color: white; }
    </style>
</head>
<body>
    <?php include 'admin_sidebar.php'; ?>
    <div class="main">
        <h2>Order Management</h2>
        <table>
            <thead><tr><th>ID</th><th>Customer</th><th>Phone</th><th>Total</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach($orders as $o): ?>
                <tr>
                    <td>#<?= $o['id'] ?></td>
                    <td><?= htmlspecialchars($o['customer_name']) ?></td>
                    <td><?= htmlspecialchars($o['customer_phone']) ?></td>
                    <td>৳ <?= number_format($o['total_amount']) ?></td>
                    <td><span class="badge <?= $o['order_status'] ?>"><?= $o['order_status'] ?></span></td>
                    <td><a href="order_details.php?id=<?= $o['id'] ?>" style="color:#00e5ff;">Manage</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>