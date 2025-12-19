<?php require 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service | <?= htmlspecialchars($siteSettings['site_name']) ?></title>
    <link rel="stylesheet" href="style.css?v=7.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>.legal-content { max-width: 800px; margin: 50px auto; color: #b0b8c4; line-height: 1.8; } .legal-content h2 { color: white; margin-top: 30px; }</style>
</head>
<body>
    <header class="main-header">
        <a href="index.php" class="logo"><div class="logo-text">CHINA <span>Product Bazar</span></div></a>
        <nav class="nav-menu" style="border:none;"><ul class="nav-list"><li><a href="index.php">Back to Home</a></li></ul></nav>
    </header>

    <div class="container legal-content">
        <h1 style="color:var(--primary); border-bottom:1px solid var(--border); padding-bottom:20px;">Terms of Service</h1>
        
        <h2>1. Acceptance of Terms</h2>
        <p>By accessing and using this website, you accept and agree to be bound by the terms and provision of this agreement.</p>

        <h2>2. Product Availability</h2>
        <p>All products are subject to availability. We reserve the right to limit the quantity of products we supply.</p>

        <h2>3. Pricing</h2>
        <p>Prices for our products are subject to change without notice. We reserve the right at any time to modify or discontinue the Service.</p>
    </div>
    
    <footer style="text-align:center; padding: 2rem; color: #718096; border-top: 1px solid var(--border);">&copy; <?= date('Y') ?> China Product Bazar.</footer>
</body>
</html>