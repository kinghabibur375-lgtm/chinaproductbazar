<?php
require 'config.php';
if (!isset($_SESSION['admin_logged_in'])) { header("Location: admin_login.php"); exit; }

// --- SAVE SETTINGS ---
if (isset($_POST['save_settings'])) {
    foreach ($_POST['settings'] as $key => $value) {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        $stmt->execute([$key, $value]);
    }
    // Handle File Uploads
    $uploads = ['logo_light', 'logo_dark', 'favicon'];
    foreach ($uploads as $field) {
        if (!empty($_FILES[$field]['name'])) {
            $ext = pathinfo($_FILES[$field]["name"], PATHINFO_EXTENSION);
            $filename = $field . "_" . time() . "." . $ext;
            if (move_uploaded_file($_FILES[$field]["tmp_name"], "uploads/" . $filename)) {
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
                $stmt->execute([$filename, $field]);
            }
        }
    }
    header("Location: admin_settings.php?status=saved"); exit;
}

// Fetch Settings
$settings = [];
$stmt = $pdo->query("SELECT * FROM settings");
while ($row = $stmt->fetch()) { $settings[$row['setting_key']] = $row['setting_value']; }
function getVal($key, $data) { return isset($data[$key]) ? htmlspecialchars($data[$key]) : ''; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Website Settings</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=12.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { display: flex; font-family: 'Inter', sans-serif; }</style>
</head>
<body>
    <?php include 'admin_sidebar.php'; ?>
    <div class="main-content">
        <h2 style="margin-bottom: 30px;">Website Configuration</h2>
        
        <form method="POST" enctype="multipart/form-data" class="settings-container">
            <div class="settings-tabs">
                <button type="button" class="tab-btn active" onclick="openTab(event, 'general')"><i class="fa-solid fa-globe"></i> General</button>
                <button type="button" class="tab-btn" onclick="openTab(event, 'design')"><i class="fa-solid fa-palette"></i> Design & Logos</button>
                <button type="button" class="tab-btn" onclick="openTab(event, 'footer')"><i class="fa-solid fa-layer-group"></i> Footer & Socials</button>
                <button type="button" class="tab-btn" onclick="openTab(event, 'contact')"><i class="fa-solid fa-address-book"></i> Contact Info</button>
                <button type="button" class="tab-btn" onclick="openTab(event, 'payment')"><i class="fa-solid fa-credit-card"></i> Payment</button>
            </div>

            <div class="settings-content">
                
                <div id="general" class="tab-pane active">
                    <div class="settings-header"><h3>General Identity</h3></div>
                    <div class="form-group"><label>Website Name</label><input type="text" name="settings[site_name]" class="admin-input" value="<?= getVal('site_name', $settings) ?>"></div>
                    <div class="form-group"><label>Tagline / Slogan</label><input type="text" name="settings[site_tagline]" class="admin-input" value="<?= getVal('site_tagline', $settings) ?>"></div>
                </div>

                <div id="design" class="tab-pane">
                    <div class="settings-header"><h3>Design & Branding</h3></div>
                    <div class="input-row">
                        <div class="form-group"><label>Primary Color</label><input type="color" name="settings[primary_color]" class="admin-input" style="height:50px; padding:5px;" value="<?= getVal('primary_color', $settings) ?>"></div>
                        <div class="form-group"><label>Secondary Color</label><input type="color" name="settings[secondary_color]" class="admin-input" style="height:50px; padding:5px;" value="<?= getVal('secondary_color', $settings) ?>"></div>
                    </div>
                    <div class="form-group"><label>Light Mode Logo</label><input type="file" name="logo_light" class="admin-input"></div>
                    <div class="form-group"><label>Dark Mode Logo</label><input type="file" name="logo_dark" class="admin-input"></div>
                    <div class="form-group"><label>Favicon</label><input type="file" name="favicon" class="admin-input"></div>
                </div>

                <div id="footer" class="tab-pane">
                    <div class="settings-header"><h3>Footer Customization</h3></div>
                    <div class="form-group">
                        <label>About Text (Footer Column 1)</label>
                        <textarea name="settings[footer_about]" class="admin-input" rows="3"><?= getVal('footer_about', $settings) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Copyright Text</label>
                        <input type="text" name="settings[footer_copyright]" class="admin-input" value="<?= getVal('footer_copyright', $settings) ?>">
                    </div>
                    <hr style="border-color:var(--border); margin:20px 0;">
                    <h4>Social Media Links</h4>
                    <div class="input-row">
                        <div class="form-group"><label>Facebook URL</label><input type="text" name="settings[social_facebook]" class="admin-input" value="<?= getVal('social_facebook', $settings) ?>"></div>
                        <div class="form-group"><label>Instagram URL</label><input type="text" name="settings[social_instagram]" class="admin-input" value="<?= getVal('social_instagram', $settings) ?>"></div>
                    </div>
                    <div class="form-group"><label>YouTube URL</label><input type="text" name="settings[social_youtube]" class="admin-input" value="<?= getVal('social_youtube', $settings) ?>"></div>
                </div>

                <div id="contact" class="tab-pane">
                    <div class="settings-header"><h3>Contact Information</h3></div>
                    <div class="input-row">
                        <div class="form-group"><label>Phone Number</label><input type="text" name="settings[site_phone]" class="admin-input" value="<?= getVal('site_phone', $settings) ?>"></div>
                        <div class="form-group"><label>Email Address</label><input type="text" name="settings[site_email]" class="admin-input" value="<?= getVal('site_email', $settings) ?>"></div>
                    </div>
                    <div class="form-group"><label>Office Address</label><textarea name="settings[site_address]" class="admin-input"><?= getVal('site_address', $settings) ?></textarea></div>
                </div>

                <div id="payment" class="tab-pane">
                    <div class="settings-header"><h3>Payment & Shipping</h3></div>
                    <div class="form-group"><label>Currency Symbol</label><input type="text" name="settings[currency]" class="admin-input" value="<?= getVal('currency', $settings) ?>"></div>
                    <div class="form-group"><label>Delivery Charge (Inside Dhaka)</label><input type="number" name="settings[delivery_charge_inside]" class="admin-input" value="<?= getVal('delivery_charge_inside', $settings) ?>"></div>
                </div>

                <button type="submit" name="save_settings" class="btn-save" style="margin-top:20px;">Save All Changes</button>
            </div>
        </form>
    </div>

    <div id="adminToast" class="admin-toast"><i class="fa-solid fa-circle-check toast-icon"></i><span class="toast-msg">Settings Saved!</span></div>
    <script>
        function openTab(evt, name) {
            document.querySelectorAll(".tab-pane").forEach(x => x.style.display = "none");
            document.querySelectorAll(".tab-btn").forEach(x => x.classList.remove("active"));
            document.getElementById(name).style.display = "block";
            evt.currentTarget.classList.add("active");
        }
        if (new URLSearchParams(window.location.search).get('status') === 'saved') {
            const t = document.getElementById("adminToast");
            setTimeout(() => { t.classList.add("show"); }, 200);
            setTimeout(() => { t.classList.remove("show"); window.history.replaceState(null, null, window.location.pathname); }, 3000);
        }
    </script>
</body>
</html>