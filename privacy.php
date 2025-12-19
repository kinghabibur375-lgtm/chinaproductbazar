<?php require 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy | <?= htmlspecialchars($siteSettings['site_name']) ?></title>
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
        <h1 style="color:var(--primary); border-bottom:1px solid var(--border); padding-bottom:20px;">Privacy Policy</h1>
        <p>Last Updated: <?= date('F Y') ?></p>

        <h2>1. Information We Collect</h2>
        <p>We collect information you provide directly to us, such as when you create an account, place an order, or contact customer support. This includes your name, email address, phone number, and shipping address.</p>

        <h2>2. How We Use Your Information</h2>
        <p>We use the information we collect to process your orders, communicate with you, and improve our services. We do not sell your personal data to third parties.</p>

        <h2>3. Data Security</h2>
        <p>We implement appropriate security measures to protect your personal data from unauthorized access or disclosure.</p>
    </div>
    
    <footer style="text-align:center; padding: 2rem; color: #718096; border-top: 1px solid var(--border);">&copy; <?= date('Y') ?> China Product Bazar.</footer>
</body>
</html>