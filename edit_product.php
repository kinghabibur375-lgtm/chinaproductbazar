<?php
require 'config.php';

// Security Check
if (!isset($_SESSION['admin_logged_in'])) { header("Location: admin_login.php"); exit; }
if (!isset($_GET['id'])) { header("Location: admin_products.php"); exit; }

$id = $_GET['id'];

// --- HANDLE UPDATE ---
if (isset($_POST['update_product'])) {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];

    if (!empty($_FILES['image']['name'])) {
        $file_ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . "." . $file_ext;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/" . $new_filename)) {
            $stmt = $pdo->prepare("UPDATE products SET title=?, price_bdt=?, stock=?, description=?, image=? WHERE id=?");
            $stmt->execute([$title, $price, $stock, $description, $new_filename, $id]);
        }
    } else {
        $stmt = $pdo->prepare("UPDATE products SET title=?, price_bdt=?, stock=?, description=? WHERE id=?");
        $stmt->execute([$title, $price, $stock, $description, $id]);
    }

    // --- NEW REDIRECT (No Popup) ---
    header("Location: admin_products.php?status=updated");
    exit;
}

// Fetch Data
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) { die("Product not found."); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=6.0">
    <style>
        body { background-color: #0b0f19; color: white; font-family: 'Inter', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .edit-container { background: #151a27; padding: 40px; border-radius: 12px; border: 1px solid #2a3b55; width: 100%; max-width: 600px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #94a3b8; font-size: 0.9rem; }
        .current-img-box { margin-bottom: 20px; text-align: center; padding: 10px; background: #0b0f19; border-radius: 8px; border: 1px dashed #2a3b55; }
        .current-img-box img { max-height: 150px; border-radius: 5px; }
    </style>
</head>
<body>

    <div class="edit-container">
        <h2 style="margin-top:0; color: #00e5ff; text-align:center;">Edit Product</h2>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="title" class="admin-input" value="<?= htmlspecialchars($product['title']) ?>" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Price (BDT)</label>
                    <input type="number" name="price" class="admin-input" value="<?= htmlspecialchars($product['price_bdt']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Stock Quantity</label>
                    <input type="number" name="stock" class="admin-input" value="<?= htmlspecialchars($product['stock']) ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="admin-input" rows="5"><?= htmlspecialchars($product['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Current Image</label>
                <div class="current-img-box">
                    <img src="uploads/<?= $product['image'] ?>" alt="Current">
                    <p style="font-size:0.8rem; color:#94a3b8; margin:5px 0 0;">Upload new image below to replace this</p>
                </div>
                <input type="file" name="image" class="admin-input" style="padding: 10px;">
            </div>

            <button type="submit" name="update_product" class="btn-save">Save Changes</button>
            <a href="admin_products.php" class="btn-cancel">Cancel</a>
        </form>
    </div>

</body>
</html>