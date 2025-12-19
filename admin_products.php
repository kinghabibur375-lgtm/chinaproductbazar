<?php
require 'config.php';
if (!isset($_SESSION['admin_logged_in'])) header("Location: admin_login.php");

// Delete Logic with Redirect
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$_GET['delete']]);
    header("Location: admin_products.php?status=deleted");
    exit;
}

$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Products | Admin</title>
    <link rel="stylesheet" href="style.css?v=6.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #0b0f19; color: white; font-family: 'Inter', sans-serif; display: flex; }
        .main { flex: 1; padding: 30px; overflow-y: auto; height: 100vh; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #151a27; border-radius: 8px; overflow: hidden; }
        th, td { padding: 15px; border-bottom: 1px solid #2a3b55; text-align: left; }
        th { color: #94a3b8; font-weight: 500; font-size: 0.9rem; background: rgba(0,0,0,0.2); }
        td { font-size: 0.95rem; vertical-align: middle; }
        
        .btn { padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 0.8rem; font-weight: 600; display: inline-block; transition: 0.2s; }
        .btn-edit { background: rgba(0, 229, 255, 0.15); color: #00e5ff; border: 1px solid #00e5ff; }
        .btn-edit:hover { background: #00e5ff; color: black; }
        .btn-del { background: rgba(255, 23, 68, 0.15); color: #ff1744; border: 1px solid #ff1744; margin-left: 5px; }
        .btn-del:hover { background: #ff1744; color: white; }
        
        .add-btn { background: #00e5ff; color: black; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; }
        .add-btn:hover { background: white; }
        
        .stock-high { color: #00c853; font-weight: bold; }
        .stock-low { color: #ffab00; font-weight: bold; }
        .stock-out { color: #ff1744; font-weight: bold; }
    </style>
</head>
<body>
    <?php include 'admin_sidebar.php'; ?>
    
    <div class="main">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h2>Product Management</h2>
            <a href="add_product.php" class="add-btn">+ Add Product</a>
        </div>

        <table>
            <thead><tr><th>Image</th><th>Name</th><th>Price</th><th>Stock</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach($products as $p): ?>
                <tr>
                    <td><img src="uploads/<?= $p['image'] ?>" width="50" height="50" style="border-radius:6px; object-fit:cover; border:1px solid #2a3b55;"></td>
                    <td>
                        <div style="font-weight:600;"><?= htmlspecialchars($p['title']) ?></div>
                        <div style="font-size:0.8rem; color:#94a3b8;">ID: #<?= $p['id'] ?></div>
                    </td>
                    <td>৳ <?= number_format($p['price_bdt']) ?></td>
                    <td>
                        <?php if($p['stock'] == 0): ?>
                            <span class="stock-out">Out of Stock</span>
                        <?php elseif($p['stock'] < 5): ?>
                            <span class="stock-low"><?= $p['stock'] ?> (Low)</span>
                        <?php else: ?>
                            <span class="stock-high"><?= $p['stock'] ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn btn-edit"><i class="fa-solid fa-pen"></i> Edit</a>
                        <a href="admin_products.php?delete=<?= $p['id'] ?>" class="btn btn-del" onclick="return confirm('Delete this product permanently?')"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div id="adminToast" class="admin-toast">
        <i class="fa-solid fa-circle-check toast-icon"></i>
        <span class="toast-msg">Operation Successful!</span>
    </div>

    <script>
        // Check URL for status
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        const toast = document.getElementById("adminToast");
        const msg = toast.querySelector(".toast-msg");
        const icon = toast.querySelector(".toast-icon");

        if (status) {
            if (status === 'updated') {
                msg.innerText = "Product updated successfully!";
            } else if (status === 'deleted') {
                msg.innerText = "Product deleted successfully!";
                toast.classList.add("delete");
                icon.className = "fa-solid fa-trash-can toast-icon";
            } else if (status === 'added') {
                msg.innerText = "New product added successfully!";
            }

            // Show Animation
            setTimeout(() => { toast.classList.add("show"); }, 200);

            // Hide after 3s
            setTimeout(() => { 
                toast.classList.remove("show");
                window.history.replaceState(null, null, window.location.pathname);
            }, 3000);
        }
    </script>
</body>
</html>