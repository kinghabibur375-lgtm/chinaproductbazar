<?php
require 'config.php';
require 'Theme.php'; // Load Engine
if (!isset($_SESSION['admin_logged_in'])) { header("Location: admin_login.php"); exit; }

$theme = new Theme($pdo);

// --- SAVE HANDLER ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Save Text Settings
    if (isset($_POST['settings'])) {
        foreach ($_POST['settings'] as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO theme_settings (setting_key, setting_value, group_name) VALUES (?, ?, 'user') ON DUPLICATE KEY UPDATE setting_value = ?");
            $stmt->execute([$key, $value, $value]);
        }
    }
    // 2. Save Files
    $files = ['logo_light', 'logo_dark', 'favicon'];
    foreach ($files as $f) {
        if (!empty($_FILES[$f]['name'])) {
            $ext = pathinfo($_FILES[$f]["name"], PATHINFO_EXTENSION);
            $name = $f . "_" . time() . "." . $ext;
            if (move_uploaded_file($_FILES[$f]["tmp_name"], "uploads/" . $name)) {
                $pdo->prepare("UPDATE theme_settings SET setting_value = ? WHERE setting_key = ?")->execute([$name, $f]);
            }
        }
    }
    header("Location: admin_theme.php?status=saved"); exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Theme Engine | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=20.0"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #0b0f19; color: white; display: flex; font-family: 'Inter'; }
        .main-content { padding: 40px; flex: 1; height: 100vh; overflow-y: auto; }
        .theme-grid { display: grid; grid-template-columns: 250px 1fr; gap: 40px; }
        .tabs { background: #151a27; border-radius: 12px; border: 1px solid #2d3748; overflow: hidden; }
        .tab-btn { display: block; width: 100%; padding: 15px 20px; background: transparent; border: none; color: #a0aec0; text-align: left; cursor: pointer; border-bottom: 1px solid #2d3748; transition: 0.3s; }
        .tab-btn:hover, .tab-btn.active { background: rgba(0, 229, 255, 0.1); color: #00e5ff; border-left: 4px solid #00e5ff; }
        .panel { display: none; background: #151a27; padding: 40px; border-radius: 12px; border: 1px solid #2d3748; }
        .panel.active { display: block; animation: fadeIn 0.3s; }
        .form-grp { margin-bottom: 25px; }
        .form-grp label { display: block; margin-bottom: 8px; color: #a0aec0; font-size: 0.9rem; }
        .inp { width: 100%; background: #0b0f19; border: 1px solid #2d3748; padding: 12px; color: white; border-radius: 6px; outline: none; }
        .inp:focus { border-color: #00e5ff; }
        .color-inp { padding: 5px; height: 50px; cursor: pointer; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
    </style>
</head>
<body>
    <?php include 'admin_sidebar.php'; ?>
    <div class="main-content">
        <h2 style="margin-bottom: 30px;">Visual Control Engine</h2>
        <form method="POST" enctype="multipart/form-data" class="theme-grid">
            
            <div class="tabs">
                <div class="tab-btn active" onclick="switchTab('branding')"><i class="fa-solid fa-star"></i> Branding</div>
                <div class="tab-btn" onclick="switchTab('colors')"><i class="fa-solid fa-palette"></i> Colors</div>
                <div class="tab-btn" onclick="switchTab('typography')"><i class="fa-solid fa-font"></i> Typography</div>
                <div class="tab-btn" onclick="switchTab('layout')"><i class="fa-solid fa-layer-group"></i> Layout & UI</div>
                <button type="submit" class="tab-btn" style="color:#00e5ff; font-weight:bold; margin-top:20px;"><i class="fa-solid fa-save"></i> Save Changes</button>
            </div>

            <div class="content-area">
                <div id="branding" class="panel active">
                    <h3>Brand Identity</h3>
                    <div class="form-grp">
                        <label>Light Mode Logo</label>
                        <input type="file" name="logo_light" class="inp">
                    </div>
                    <div class="form-grp">
                        <label>Dark Mode Logo</label>
                        <input type="file" name="logo_dark" class="inp">
                    </div>
                    <div class="form-grp">
                        <label>Logo Width (e.g. 150px)</label>
                        <input type="text" name="settings[logo_width]" class="inp" value="<?= $theme->get('logo_width') ?>">
                    </div>
                </div>

                <div id="colors" class="panel">
                    <h3>Global Color System</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <h4 style="color:#00e5ff; border-bottom:1px solid #2d3748; padding-bottom:10px;">Light Mode</h4>
                            <div class="form-grp"><label>Primary</label><input type="color" name="settings[primary]" class="inp color-inp" value="<?= $theme->get('primary') ?>"></div>
                            <div class="form-grp"><label>Background</label><input type="color" name="settings[bg_body]" class="inp color-inp" value="<?= $theme->get('bg_body') ?>"></div>
                            <div class="form-grp"><label>Card Bg</label><input type="color" name="settings[bg_card]" class="inp color-inp" value="<?= $theme->get('bg_card') ?>"></div>
                            <div class="form-grp"><label>Text Main</label><input type="color" name="settings[text_main]" class="inp color-inp" value="<?= $theme->get('text_main') ?>"></div>
                        </div>
                        <div>
                            <h4 style="color:#a0aec0; border-bottom:1px solid #2d3748; padding-bottom:10px;">Dark Mode</h4>
                            <div class="form-grp"><label>Primary</label><input type="color" name="settings[dm_primary]" class="inp color-inp" value="<?= $theme->get('dm_primary') ?>"></div>
                            <div class="form-grp"><label>Background</label><input type="color" name="settings[dm_bg_body]" class="inp color-inp" value="<?= $theme->get('dm_bg_body') ?>"></div>
                            <div class="form-grp"><label>Card Bg</label><input type="color" name="settings[dm_bg_card]" class="inp color-inp" value="<?= $theme->get('dm_bg_card') ?>"></div>
                            <div class="form-grp"><label>Text Main</label><input type="color" name="settings[dm_text_main]" class="inp color-inp" value="<?= $theme->get('dm_text_main') ?>"></div>
                        </div>
                    </div>
                </div>

                <div id="typography" class="panel">
                    <h3>Typography</h3>
                    <div class="form-grp"><label>Headings Font (Google Fonts Name)</label><input type="text" name="settings[font_heading]" class="inp" value="<?= $theme->get('font_heading') ?>"></div>
                    <div class="form-grp"><label>Body Font (Google Fonts Name)</label><input type="text" name="settings[font_body]" class="inp" value="<?= $theme->get('font_body') ?>"></div>
                </div>

                <div id="layout" class="panel">
                    <h3>Layout & Components</h3>
                    <div class="form-grp"><label>Container Width</label><input type="text" name="settings[container_width]" class="inp" value="<?= $theme->get('container_width') ?>"></div>
                    <div class="form-grp">
                        <label>Border Radius</label>
                        <select name="settings[border_radius]" class="inp">
                            <option value="0px" <?= $theme->get('border_radius')=='0px'?'selected':'' ?>>Sharp (0px)</option>
                            <option value="8px" <?= $theme->get('border_radius')=='8px'?'selected':'' ?>>Standard (8px)</option>
                            <option value="50px" <?= $theme->get('border_radius')=='50px'?'selected':'' ?>>Round (50px)</option>
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function switchTab(id) {
            document.querySelectorAll('.panel').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            document.getElementById(id).classList.add('active');
            event.target.classList.add('active');
        }
    </script>
</body>
</html>