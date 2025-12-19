<?php
// Fetch current admin avatar for the sidebar
$sidebar_avatar_src = "";
if (isset($_SESSION['admin_id'])) {
    $stmt_side = $pdo->prepare("SELECT avatar FROM admins WHERE id = ?");
    $stmt_side->execute([$_SESSION['admin_id']]);
    $side_avatar = $stmt_side->fetchColumn();
    if ($side_avatar && file_exists("uploads/" . $side_avatar)) {
        $sidebar_avatar_src = "uploads/" . $side_avatar;
    }
}
?>
<div class="sidebar" style="width: 260px; background: #151a27; border-right: 1px solid #2a3b55; padding: 20px; display: flex; flex-direction: column;">
    
    <div style="font-size: 1.5rem; font-weight: 800; color: white; margin-bottom: 30px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-bag-shopping" style="color: #00e5ff;"></i> ADMIN
    </div>

    <?php if(isset($_SESSION['admin_logged_in'])): ?>
    <a href="admin_profile.php" style="display:flex; align-items:center; gap:15px; margin-bottom:30px; padding: 15px; background: #0b0f19; border-radius: 12px; border:1px solid #2a3b55; text-decoration:none;">
        <?php if($sidebar_avatar_src): ?>
            <img src="<?= $sidebar_avatar_src ?>" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #00e5ff;">
        <?php else: ?>
            <div style="width: 45px; height: 45px; background: #2a3b55; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem;"><i class="fa-solid fa-user"></i></div>
        <?php endif; ?>
        <div style="overflow:hidden;">
            <div style="font-weight: 600; font-size: 0.95rem; color:white; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></div>
            <div style="font-size: 0.8rem; color: #94a3b8;">Edit Profile</div>
        </div>
    </a>
    <?php endif; ?>
    
    <div style="font-size:0.75rem; color:#94a3b8; margin-bottom:10px; font-weight:600; letter-spacing:1px;">MAIN MENU</div>
    <ul style="list-style: none; padding: 0;">
        <li style="margin-bottom: 5px;"><a href="admin_panel.php" style="display: flex; gap: 12px; padding: 12px; color: #94a3b8; text-decoration: none; border-radius: 8px; transition:0.3s;"><i class="fa-solid fa-chart-pie" style="width:20px;"></i> Dashboard</a></li>
        <li style="margin-bottom: 5px;"><a href="admin_orders.php" style="display: flex; gap: 12px; padding: 12px; color: #94a3b8; text-decoration: none; border-radius: 8px; transition:0.3s;"><i class="fa-solid fa-box-open" style="width:20px;"></i> Orders</a></li>
        <li style="margin-bottom: 5px;"><a href="admin_products.php" style="display: flex; gap: 12px; padding: 12px; color: #94a3b8; text-decoration: none; border-radius: 8px; transition:0.3s;"><i class="fa-solid fa-tags" style="width:20px;"></i> Products</a></li>
        <li style="margin-bottom: 5px;"><a href="admin_customers.php" style="display: flex; gap: 12px; padding: 12px; color: #94a3b8; text-decoration: none; border-radius: 8px; transition:0.3s;"><i class="fa-solid fa-users" style="width:20px;"></i> Customers</a></li>
        
        <li style="margin-bottom: 5px;"><a href="admin_settings.php" style="display: flex; gap: 12px; padding: 12px; color: #94a3b8; text-decoration: none; border-radius: 8px; transition:0.3s;"><i class="fa-solid fa-gear" style="width:20px;"></i> Settings</a></li>
        <li style="margin-bottom: 5px;"><a href="index.php" target="_blank" style="display: flex; gap: 12px; padding: 12px; color: #94a3b8; text-decoration: none; border-radius: 8px; transition:0.3s;"><i class="fa-solid fa-globe" style="width:20px;"></i> View Website</a></li>
    </ul>

    <div style="margin-top:auto;">
        <a href="logout.php" style="color: #ff1744; text-decoration: none; display: flex; gap: 10px; padding: 12px; border-radius:8px; transition:0.3s;"><i class="fa-solid fa-right-from-bracket" style="width:20px;"></i> Logout</a>
    </div>
</div>