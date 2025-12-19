<?php
require 'config.php';

// Security Check
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php"); exit;
}

// Fetch Admin Details
$stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id'] ?? 1]); 
$admin = $stmt->fetch();

// Avatar Logic
$admin_avatar = "https://via.placeholder.com/150/00e5ff/000000?text=ADMIN"; // Default
if (!empty($admin['avatar']) && file_exists('uploads/' . $admin['avatar'])) {
    $admin_avatar = 'uploads/' . $admin['avatar'];
}

// --- DASHBOARD DATA ---
$revenue = $pdo->query("SELECT SUM(total_amount) FROM orders")->fetchColumn() ?? 0;
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pending_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'Pending'")->fetchColumn();
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$recent_orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5")->fetchAll();
$top_products = $pdo->query("SELECT * FROM products ORDER BY stock ASC LIMIT 4")->fetchAll();

// Chart Data (Last 7 Days)
$chart_labels = [];
$chart_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chart_labels[] = date('M d', strtotime("-$i days"));
    $stmt = $pdo->prepare("SELECT SUM(total_amount) FROM orders WHERE DATE(order_date) = ?");
    $stmt->execute([$date]);
    $chart_data[] = $stmt->fetchColumn() ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="style.css?v=7.0">
    <style>
        :root { --bg-dark: #0b0f19; --bg-card: #151a27; --primary: #00e5ff; --text-main: #ffffff; --text-muted: #94a3b8; --border: #2a3b55; --success: #00c853; --warning: #ffab00; --danger: #ff1744; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-dark); color: var(--text-main); display: flex; height: 100vh; overflow: hidden; }
        a { text-decoration: none; color: inherit; }
        
        /* Main Content Styles */
        .main-content { flex: 1; overflow-y: auto; padding: 30px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        /* User Profile in Header */
        .user-profile { display: flex; align-items: center; gap: 15px; cursor: pointer; text-decoration: none; color: white; transition: 0.3s; }
        .user-profile:hover { opacity: 0.8; }
        .user-avatar { width: 45px; height: 45px; background: #2a3b55; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; overflow: hidden; border: 2px solid var(--primary); }
        .user-avatar img { width: 100%; height: 100%; object-fit: cover; }

        /* Metrics */
        .metrics-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .metric-card { background: var(--bg-card); padding: 25px; border-radius: 12px; border: 1px solid var(--border); position: relative; }
        
        /* Dashboard Split */
        .dashboard-split { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 30px; }
        .chart-box, .top-products-box, .table-box { background: var(--bg-card); padding: 25px; border-radius: 12px; border: 1px solid var(--border); }
        
        /* Tables */
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th { text-align: left; color: var(--text-muted); padding: 15px; border-bottom: 1px solid var(--border); }
        .admin-table td { padding: 15px; border-bottom: 1px solid #1f2937; }
        .status-badge { padding: 5px 10px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .status-Pending { background: rgba(255, 171, 0, 0.15); color: var(--warning); }
        .status-Shipped { background: rgba(0, 229, 255, 0.15); color: var(--primary); }
        .status-Delivered { background: rgba(0, 200, 83, 0.15); color: var(--success); }
        .status-Cancelled { background: rgba(255, 23, 68, 0.15); color: var(--danger); }
        
        @media (max-width: 1024px) { .metrics-grid, .dashboard-split { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <?php include 'admin_sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <h2>Overview</h2>
            
            <a href="admin_profile.php" class="user-profile">
                <div style="text-align: right;">
                    <div style="font-weight: 600; font-size: 0.9rem;"><?= htmlspecialchars($admin['username']) ?></div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">Super Admin</div>
                </div>
                <div class="user-avatar">
                    <?php if(!empty($admin['avatar']) && file_exists('uploads/'.$admin['avatar'])): ?>
                        <img src="uploads/<?= htmlspecialchars($admin['avatar']) ?>" alt="Profile">
                    <?php else: ?>
                        <i class="fa-solid fa-user"></i>
                    <?php endif; ?>
                </div>
            </a>
        </div>

        <div class="metrics-grid">
            <div class="metric-card">
                <h3 style="font-size:2rem; margin-bottom:5px; color:white;">৳ <?= number_format($revenue) ?></h3>
                <p style="color:#94a3b8;">Total Revenue</p>
            </div>
            <div class="metric-card">
                <h3 style="font-size:2rem; margin-bottom:5px; color:white;"><?= $pending_orders ?> <span style="font-size:1rem; color:#ffab00;">New</span></h3>
                <p style="color:#94a3b8;">Pending Orders</p>
            </div>
            <div class="metric-card">
                <h3 style="font-size:2rem; margin-bottom:5px; color:white;"><?= $total_orders ?></h3>
                <p style="color:#94a3b8;">Total Orders</p>
            </div>
            <div class="metric-card">
                <h3 style="font-size:2rem; margin-bottom:5px; color:white;"><?= $total_products ?></h3>
                <p style="color:#94a3b8;">Total Products</p>
            </div>
        </div>

        <div class="dashboard-split">
            <div class="chart-box">
                <h4 style="margin-bottom:20px;">Sales Overview (Last 7 Days)</h4>
                <canvas id="salesChart" height="120"></canvas>
            </div>
            <div class="top-products-box">
                <h4 style="margin-bottom:20px;">Top Products (Low Stock)</h4>
                <?php foreach($top_products as $p): ?>
                <div style="display:flex; align-items:center; gap:15px; margin-bottom:15px; padding-bottom:15px; border-bottom:1px solid #2a3b55;">
                    <img src="uploads/<?= $p['image'] ?>" style="width:50px; height:50px; border-radius:8px; object-fit:cover;">
                    <div>
                        <div style="font-size:0.95rem; font-weight:500;"><?= htmlspecialchars($p['title']) ?></div>
                        <div style="font-size:0.85rem; color:#94a3b8;">Stock: <?= $p['stock'] ?></div>
                    </div>
                    <div style="margin-left:auto; font-weight:600; color:#00e5ff;">৳ <?= $p['price_bdt'] ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="table-box">
            <h4 style="margin-bottom:20px;">Recent Orders</h4>
            <table class="admin-table">
                <thead><tr><th>ID</th><th>Customer</th><th>Date</th><th>Amount</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach($recent_orders as $o): ?>
                    <tr>
                        <td>#<?= $o['id'] ?></td>
                        <td><?= htmlspecialchars($o['customer_name']) ?></td>
                        <td><?= date('M d', strtotime($o['order_date'])) ?></td>
                        <td>৳ <?= number_format($o['total_amount']) ?></td>
                        <td><span class="status-badge status-<?= $o['order_status'] ?>"><?= $o['order_status'] ?></span></td>
                        <td><a href="order_details.php?id=<?= $o['id'] ?>" style="color:#00e5ff;">View</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($chart_labels) ?>,
                datasets: [{
                    label: 'Revenue',
                    data: <?= json_encode($chart_data) ?>,
                    borderColor: '#00e5ff',
                    backgroundColor: 'rgba(0, 229, 255, 0.1)',
                    fill: true, tension: 0.4
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false }, ticks: { color: '#94a3b8' } }, y: { grid: { color: '#2a3b55' }, ticks: { color: '#94a3b8' } } } }
        });
    </script>
</body>
</html>