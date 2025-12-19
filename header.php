<?php
if (!isset($pdo)) { require_once 'config.php'; }
$settings = [];
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    while ($row = $stmt->fetch()) { $settings[$row['setting_key']] = $row['setting_value']; }
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($settings['site_name'] ?? 'China Product Bazar') ?></title>
    <link rel="stylesheet" href="style.css?v=50.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="top-bar">
        <div><i class="fa-solid fa-bolt" style="color:var(--primary)"></i> Flash Sale Active!</div>
        <div><a href="track_order.php">Track Order</a> | <a href="admin_login.php">Admin Panel</a></div>
    </div>

    <header class="main-header">
        
        <a href="index.php" class="logo">
            <div class="brand-box">
                <span class="brand-top">CHINA</span>
                <span class="brand-bot">Product Bazar</span>
            </div>
        </a>

        <div class="desktop-search">
            <form action="shop.php" method="GET" style="position:relative;">
                <input type="text" id="desktopSearchInput" name="search" class="search-input" placeholder="Search for gadgets..." autocomplete="off">
                <button type="submit" class="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
                <div id="desktopResults" class="smart-results"></div>
            </form>
        </div>

        <div class="header-actions">
            <div class="action-btn mobile-search-trigger" onclick="toggleSearch()">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>

            <div class="action-btn" id="themeToggleBtn">
                <i class="fa-solid fa-sun" id="themeIcon"></i>
            </div>
            
            <a href="cart.php" class="action-btn">
                <i class="fa-solid fa-cart-shopping"></i>
                <?php if(isset($_SESSION['cart']) && count($_SESSION['cart'])>0): ?>
                    <span class="cart-badge"><?= array_sum($_SESSION['cart']) ?></span>
                <?php endif; ?>
            </a>

            <div class="action-btn mobile-menu-btn" onclick="toggleMenu()">
                <i class="fa-solid fa-bars"></i>
            </div>
        </div>
    </header>

    <nav class="nav-menu" id="mobileNav">
        <div class="nav-close-btn" onclick="toggleMenu()"><i class="fa-solid fa-xmark"></i></div>
        <ul class="nav-list">
            <li><a href="index.php">Home</a></li>
            <li><a href="shop.php">Shop</a></li>
            <li><a href="track_order.php">Track Order</a></li>
            <li class="mobile-only"><a href="cart.php">My Cart</a></li>
            <li class="mobile-only"><a href="admin_login.php" style="color:#00e5ff;">Admin Login</a></li>
        </ul>
    </nav>

    <div id="searchOverlay" class="search-overlay">
        <div class="close-search" onclick="toggleSearch()"><i class="fa-solid fa-xmark"></i></div>
        <form action="shop.php" method="GET" style="width:100%; display:flex; justify-content:center;">
            <input type="text" name="search" id="mobileSearchInput" class="search-input-lg" placeholder="Search..." autocomplete="off">
        </form>
        <div id="mobileResults" style="width:90%; max-width:600px; margin-top:20px;"></div>
    </div>

    <script>
        // THEME LOGIC
        const themeBtn = document.getElementById('themeToggleBtn');
        const themeIcon = document.getElementById('themeIcon');
        const html = document.documentElement;

        function applyTheme(theme) {
            if (theme === 'light') {
                html.setAttribute('data-theme', 'light');
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
                localStorage.setItem('theme', 'light');
            } else {
                html.removeAttribute('data-theme');
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
                localStorage.setItem('theme', 'dark');
            }
        }
        const savedTheme = localStorage.getItem('theme') || 'dark';
        applyTheme(savedTheme);
        themeBtn.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-theme') === 'light' ? 'light' : 'dark';
            applyTheme(currentTheme === 'dark' ? 'light' : 'dark');
        });

        // MENU & SEARCH LOGIC
        function toggleSearch() {
            const el = document.getElementById('searchOverlay');
            el.classList.toggle('active');
            if(el.classList.contains('active')) document.getElementById('mobileSearchInput').focus();
        }
        function toggleMenu() { document.getElementById('mobileNav').classList.toggle('active'); }
        
        function enableSmartSearch(inputId, resultId) {
            const input = document.getElementById(inputId);
            if(!input) return;
            input.addEventListener('keyup', function() {
                let q = this.value;
                let box = document.getElementById(resultId);
                if (q.length > 1) {
                    let f = new FormData(); f.append('query', q);
                    fetch('search_suggest.php', { method: 'POST', body: f })
                    .then(r => r.text()).then(data => { box.innerHTML = data; box.classList.add('active'); });
                } else { box.classList.remove('active'); }
            });
            document.addEventListener('click', e => { if (e.target.id !== inputId) document.getElementById(resultId).classList.remove('active'); });
        }
        enableSmartSearch('desktopSearchInput', 'desktopResults');
        enableSmartSearch('mobileSearchInput', 'mobileResults');
    </script>
</body>
</html>