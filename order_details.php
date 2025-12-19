<?php
require 'config.php';

// Security Check
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php"); exit;
}

if (!isset($_GET['id'])) {
    header("Location: admin_orders.php"); exit;
}

$order_id = $_GET['id'];

// --- 1. HANDLE STATUS UPDATE ---
$msg = "";
if (isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
    
    if ($stmt->execute([$new_status, $order_id])) {
        $msg = "Status Updated to <strong>$new_status</strong>";
    } else {
        $msg = "Failed to update status.";
    }
}

// --- 2. FETCH DATA ---
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) { die("Order not found."); }

// Fetch Items
$stmt_items = $pdo->prepare("SELECT oi.*, p.title FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE order_id = ?");
$stmt_items->execute([$order_id]);
$items = $stmt_items->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order #<?= $order_id ?> | Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* --- 1. ADMIN INTERFACE STYLES (Screen Only) --- */
        body {
            background-color: #0b0f19; /* Dark Background */
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        /* Top Action Bar */
        .admin-toolbar {
            width: 100%;
            background: #151a27;
            border-bottom: 1px solid #2a3b55;
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }

        .status-form {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status-select {
            background: #0b0f19;
            color: white;
            border: 1px solid #2a3b55;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.9rem;
            outline: none;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-update { background: #00e5ff; color: #000; }
        .btn-update:hover { background: white; }

        .btn-print { background: #2a3b55; color: white; border: 1px solid #2a3b55; }
        .btn-print:hover { border-color: white; }

        .btn-back { color: #94a3b8; font-size: 0.9rem; margin-right: 15px; }
        .btn-back:hover { color: white; }

        .feedback-msg {
            background: rgba(0, 200, 83, 0.2);
            color: #00c853;
            padding: 8px 15px;
            border-radius: 6px;
            font-size: 0.85rem;
            border: 1px solid #00c853;
        }

        /* --- 2. THE INVOICE PAPER (What gets printed) --- */
        .invoice-container {
            background: white;
            color: #333;
            width: 210mm; /* A4 Width */
            min-height: 297mm; /* A4 Height */
            margin: 30px auto;
            padding: 40px;
            box-shadow: 0 0 50px rgba(0,0,0,0.5);
            position: relative;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #00e5ff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .inv-logo h2 { margin: 0; color: #0d47a1; text-transform: uppercase; font-size: 1.8rem; }
        .inv-logo p { margin: 5px 0 0; color: #666; font-size: 0.9rem; }

        .inv-info { text-align: right; }
        .inv-info h1 { margin: 0; color: #333; letter-spacing: 2px; }
        .inv-meta { margin-top: 10px; font-size: 0.95rem; color: #555; }

        .billing-grid { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .bill-to h4 { color: #0d47a1; margin-bottom: 10px; text-transform: uppercase; font-size: 0.8rem; }
        .bill-to p { margin: 0; line-height: 1.5; font-size: 0.95rem; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background: #f4f6f8; color: #333; font-weight: 600; text-align: left; padding: 12px; font-size: 0.9rem; border-bottom: 2px solid #ddd; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 0.9rem; }
        .total-row td { font-weight: bold; font-size: 1.1rem; color: #000; border-top: 2px solid #333; }

        .invoice-footer {
            margin-top: 50px;
            text-align: center;
            font-size: 0.8rem;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        /* --- 3. PRINT MODE (Hides Admin UI) --- */
        @media print {
            body { background: white; }
            .admin-toolbar { display: none !important; }
            .invoice-container { margin: 0; box-shadow: none; border: none; width: 100%; height: auto; }
            @page { margin: 0; }
        }
    </style>
</head>
<body>

    <div class="admin-toolbar">
        <div style="display:flex; align-items:center;">
            <a href="admin_orders.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Back to Orders</a>
            <?php if($msg): ?>
                <span class="feedback-msg"><?= $msg ?></span>
            <?php endif; ?>
        </div>

        <form method="POST" class="status-form">
            <label style="color:#94a3b8; font-size:0.9rem;">Status:</label>
            <select name="status" class="status-select">
                <option value="Pending" <?= $order['order_status']=='Pending'?'selected':'' ?>>Pending</option>
                <option value="Processing" <?= $order['order_status']=='Processing'?'selected':'' ?>>Processing</option>
                <option value="Shipped" <?= $order['order_status']=='Shipped'?'selected':'' ?>>Shipped</option>
                <option value="Delivered" <?= $order['order_status']=='Delivered'?'selected':'' ?>>Delivered</option>
                <option value="Cancelled" <?= $order['order_status']=='Cancelled'?'selected':'' ?>>Cancelled</option>
            </select>
            <button type="submit" name="update_status" class="btn btn-update">
                <i class="fa-solid fa-check"></i> Update
            </button>
        </form>

        <button onclick="window.print()" class="btn btn-print">
            <i class="fa-solid fa-print"></i> Print Invoice
        </button>
    </div>

    <div class="invoice-container">
        
        <div class="invoice-header">
            <div class="inv-logo">
                <h2><?= htmlspecialchars($siteSettings['site_name']) ?></h2>
                <p>Premium Gadgets & Lifestyle</p>
            </div>
            <div class="inv-info">
                <h1>INVOICE</h1>
                <div class="inv-meta">
                    <strong>Order ID:</strong> #<?= $order['id'] ?><br>
                    <strong>Date:</strong> <?= date('d M Y', strtotime($order['order_date'])) ?><br>
                    <strong>Status:</strong> <?= $order['order_status'] ?>
                </div>
            </div>
        </div>

        <div class="billing-grid">
            <div class="bill-to">
                <h4>Bill To:</h4>
                <p>
                    <strong><?= htmlspecialchars($order['customer_name']) ?></strong><br>
                    Phone: <?= htmlspecialchars($order['customer_phone']) ?><br>
                    Address: <?= nl2br(htmlspecialchars($order['customer_address'])) ?>
                </p>
            </div>
            <div class="bill-to" style="text-align:right;">
                <h4>Payment Method:</h4>
                <p>
                    <?= $order['payment_method'] ?><br>
                    Currency: BDT (৳)
                </p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th style="text-align:center;">Qty</th>
                    <th style="text-align:right;">Price</th>
                    <th style="text-align:right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['title']) ?></td>
                    <td style="text-align:center;"><?= $item['quantity'] ?></td>
                    <td style="text-align:right;">৳ <?= number_format($item['price']) ?></td>
                    <td style="text-align:right;">৳ <?= number_format($item['price'] * $item['quantity']) ?></td>
                </tr>
                <?php endforeach; ?>
                
                <tr class="total-row">
                    <td colspan="3" style="text-align:right; padding-top:20px;">Grand Total:</td>
                    <td style="text-align:right; padding-top:20px; color:#0d47a1;">৳ <?= number_format($order['total_amount']) ?></td>
                </tr>
            </tbody>
        </table>

        <div class="invoice-footer">
            <p>Thank you for shopping with <?= htmlspecialchars($siteSettings['site_name']) ?>!</p>
            <p>For support, contact us at: <strong>support@chinaproductbazar.com</strong> | <strong>+880 1874648952</strong></p>
        </div>

    </div>

</body>
</html>