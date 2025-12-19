<?php
require 'config.php';
if (!isset($_SESSION['admin_logged_in'])) { header("Location: admin_login.php"); exit; }

if (isset($_POST['submit_product'])) {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $desc  = $_POST['description'];
    
    $file_ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
    $new_filename = uniqid() . "." . $file_ext;
    
    if (move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/" . $new_filename)) {
        $stmt = $pdo->prepare("INSERT INTO products (title, price_bdt, stock, description, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $price, $stock, $desc, $new_filename]);
        
        // --- NEW REDIRECT (No Popup) ---
        header("Location: admin_products.php?status=added");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Product</title>
    <link rel="stylesheet" href="style.css?v=6.0">
    <style>body { background: #0b0f19; color: white; display: flex; justify-content: center; align-items: center; height: 100vh; }</style>
</head>
<body>
    <div style="background: #151a27; padding: 40px; border-radius: 12px; width: 500px; border: 1px solid #2a3b55;">
        <h2 style="margin-top:0; margin-bottom: 20px; color: #00e5ff;">Add New Product</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group"><label>Product Title</label><input type="text" name="title" class="admin-input" required></div>
            <div class="form-group"><label>Price (BDT)</label><input type="number" name="price" class="admin-input" required></div>
            <div class="form-group"><label>Stock Quantity</label><input type="number" name="stock" class="admin-input" required></div>
            <div class="form-group"><label>Description</label><textarea name="description" class="admin-input" rows="4"></textarea></div>
            <div class="form-group"><label>Image</label><input type="file" name="image" class="admin-input" style="padding: 10px;" required></div>
            <div style="display:flex; gap:10px; margin-top:20px;">
                <button type="submit" name="submit_product" class="btn-save">Save Product</button>
                <a href="admin_products.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>