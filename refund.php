<?php require 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refund Policy | <?= htmlspecialchars($siteSettings['site_name']) ?></title>
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
        <h1 style="color:var(--primary); border-bottom:1px solid var(--border); padding-bottom:20px;">Refund & Return Policy</h1>
        
        <h2>1. Return Eligibility</h2>
        <p>Our policy lasts 7 days. If 7 days have gone by since your purchase, unfortunately, we can’t offer you a refund or exchange.</p>

        <h2>2. Conditions for Return</h2>
        <p>To be eligible for a return, your item must be unused and in the same condition that you received it. It must also be in the original packaging.</p>

        <h2>3. Refund Process</h2>
        <p>Once your return is received and inspected, we will send you an email to notify you that we have received your returned item. If approved, your refund will be processed via your original payment method.</p>
    </div>
    
    <footer style="text-align:center; padding: 2rem; color: #718096; border-top: 1px solid var(--border);">&copy; <?= date('Y') ?> China Product Bazar.</footer>
</body>
</html>