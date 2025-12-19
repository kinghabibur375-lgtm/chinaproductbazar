<?php
require 'config.php';

// Security Check
if (!isset($_SESSION['admin_logged_in'])) { header("Location: admin_login.php"); exit; }

$admin_id = $_SESSION['admin_id'] ?? 1;
$msg_type = "";
$msg_text = "";

// --- 1. HANDLE IMAGE REMOVAL ---
if (isset($_POST['remove_avatar'])) {
    $stmt = $pdo->prepare("SELECT avatar FROM admins WHERE id = ?");
    $stmt->execute([$admin_id]);
    $current_avatar = $stmt->fetchColumn();
    
    if ($current_avatar && file_exists("uploads/" . $current_avatar)) {
        unlink("uploads/" . $current_avatar); // Delete file
    }
    
    $pdo->prepare("UPDATE admins SET avatar = NULL WHERE id = ?")->execute([$admin_id]);
    header("Location: admin_profile.php?status=avatar_removed"); exit;
}

// --- 2. HANDLE GENERAL PROFILE UPDATE ---
if (isset($_POST['update_profile'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $username = $_POST['username'];
    
    $avatar_sql_part = "";
    $params = [$full_name, $email, $phone, $address, $username];

    // Handle Image Upload if exists
    if (!empty($_FILES['avatar']['name'])) {
        $file_ext = pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION);
        $new_filename = "admin_" . $admin_id . "_" . uniqid() . "." . $file_ext;
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], "uploads/" . $new_filename)) {
            $avatar_sql_part = ", avatar = ?";
            $params[] = $new_filename;
        }
    }
    
    $params[] = $admin_id; // Add ID for WHERE clause

    $sql = "UPDATE admins SET full_name = ?, email = ?, phone = ?, address = ?, username = ? $avatar_sql_part WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    // Update session username just in case
    $_SESSION['admin_username'] = $username;
    
    header("Location: admin_profile.php?status=profile_updated"); exit;
}

// --- 3. HANDLE PASSWORD CHANGE ---
if (isset($_POST['update_password'])) {
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    $stmt = $pdo->prepare("SELECT password FROM admins WHERE id = ?");
    $stmt->execute([$admin_id]);
    $db_pass = $stmt->fetchColumn();

    if (!password_verify($current_pass, $db_pass)) {
        header("Location: admin_profile.php?status=pass_error_current"); exit;
    } elseif ($new_pass !== $confirm_pass) {
        header("Location: admin_profile.php?status=pass_error_match"); exit;
    } elseif (strlen($new_pass) < 6) {
        header("Location: admin_profile.php?status=pass_error_short"); exit;
    } else {
        $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?")->execute([$new_hash, $admin_id]);
        header("Location: admin_profile.php?status=password_updated"); exit;
    }
}

// --- FETCH CURRENT ADMIN DATA ---
$stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch();
// Use a default placeholder if no avatar is set
$avatar_src = $admin['avatar'] ? "uploads/".$admin['avatar'] : "https://via.placeholder.com/150/00e5ff/000000?text=ADMIN";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile | Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=7.0"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Page Specific Styles overriding/adding to master style.css */
        body { background: var(--bg-body); display: flex; }
        .main-content { flex: 1; padding: 30px; height: 100vh; overflow-y: auto; }

        .profile-layout {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
            align-items: start;
        }

        /* Profile Summary Card (Left) */
        .profile-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 30px;
            text-align: center;
        }
        .profile-avatar-lg {
            width: 120px; height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary);
            margin-bottom: 20px;
            padding: 3px;
            background: var(--bg-body);
        }
        .profile-name { font-size: 1.3rem; font-weight: 700; color: white; margin-bottom: 5px; }
        .profile-role { color: var(--text-gray); font-size: 0.9rem; margin-bottom: 20px; }
        .profile-since { font-size: 0.8rem; color: var(--text-gray); border-top: 1px solid var(--border); padding-top: 15px; }

        /* Forms Section (Right) */
        .settings-section {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
        }
        .section-title { font-size: 1.1rem; font-weight: 600; color: white; margin-bottom: 25px; padding-bottom: 10px; border-bottom: 1px solid var(--border); }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: var(--text-gray); font-size: 0.9rem; font-weight: 500; }
        
        /* Avatar Edit Controls */
        .avatar-upload-box { display: flex; align-items: center; gap: 20px; margin-bottom: 20px; }
        .avatar-preview-sm { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border); }
        .file-input-wrapper { position: relative; overflow: hidden; display: inline-block; }
        .btn-upload { border: 1px solid var(--primary); color: var(--primary); padding: 8px 15px; border-radius: 6px; cursor: pointer; font-size: 0.9rem; font-weight: 600; }
        .file-input-wrapper input[type=file] { position: absolute; left: 0; top: 0; opacity: 0; cursor: pointer; height: 100%; width: 100%; }
        .btn-remove { background: transparent; border: none; color: var(--danger); font-size: 0.9rem; cursor: pointer; padding: 8px; }
        .btn-remove:hover { text-decoration: underline; }

        .btn-primary { background: var(--primary); color: black; border: none; padding: 12px 25px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .btn-primary:hover { background: white; }

        @media (max-width: 992px) { .profile-layout { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <?php include 'admin_sidebar.php'; ?>

    <div class="main-content">
        <h2 style="margin-bottom: 30px; color: white;">My Profile</h2>

        <div class="profile-layout">
            
            <div class="profile-card">
                <img src="<?= $avatar_src ?>" alt="Profile" class="profile-avatar-lg">
                <div class="profile-name"><?= htmlspecialchars($admin['full_name'] ?: $admin['username']) ?></div>
                <div class="profile-role">Super Administrator</div>
                
                <div style="text-align: left; margin-top: 30px; color: var(--text-gray); font-size: 0.9rem;">
                    <p><i class="fa-solid fa-envelope" style="width:20px;"></i> <?= htmlspecialchars($admin['email'] ?: 'Not set') ?></p>
                    <p><i class="fa-solid fa-phone" style="width:20px;"></i> <?= htmlspecialchars($admin['phone'] ?: 'Not set') ?></p>
                </div>

                <div class="profile-since">
                    Account ID: #<?= str_pad($admin['id'], 4, '0', STR_PAD_LEFT) ?>
                </div>
            </div>

            <div class="profile-forms">
                
                <div class="settings-section">
                    <h3 class="section-title"><i class="fa-solid fa-user-gear"></i> General Information</h3>
                    
                    <form method="POST" enctype="multipart/form-data">
                        
                        <div class="form-group">
                            <label>Profile Image</label>
                            <div class="avatar-upload-box">
                                <img src="<?= $avatar_src ?>" class="avatar-preview-sm" id="avatarPreview">
                                <div>
                                    <div class="file-input-wrapper">
                                        <button class="btn-upload" type="button">Change Image</button>
                                        <input type="file" name="avatar" accept="image/*" onchange="document.getElementById('avatarPreview').src = window.URL.createObjectURL(this.files[0])">
                                    </div>
                                    <?php if($admin['avatar']): ?>
                                        <button type="submit" name="remove_avatar" class="btn-remove"><i class="fa-solid fa-trash"></i> Remove</button>
                                    <?php endif; ?>
                                    <div style="font-size:0.8rem; color:var(--text-gray); margin-top:5px;">JPG, PNG or GIF. Max size 2MB.</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="full_name" class="admin-input" value="<?= htmlspecialchars($admin['full_name']) ?>" placeholder="John Doe">
                            </div>
                            <div class="form-group">
                                <label>Username (Login ID)</label>
                                <input type="text" name="username" class="admin-input" value="<?= htmlspecialchars($admin['username']) ?>" required>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" name="email" class="admin-input" value="<?= htmlspecialchars($admin['email']) ?>" placeholder="admin@example.com">
                            </div>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text" name="phone" class="admin-input" value="<?= htmlspecialchars($admin['phone']) ?>" placeholder="+880 1XXX-XXXXXX">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address" class="admin-input" rows="3" placeholder="Office address..."><?= htmlspecialchars($admin['address']) ?></textarea>
                        </div>

                        <button type="submit" name="update_profile" class="btn-primary">Update Information</button>
                    </form>
                </div>

                <div class="settings-section" style="border-color: rgba(255, 23, 68, 0.3);">
                    <h3 class="section-title" style="color: var(--danger);"><i class="fa-solid fa-shield-halved"></i> Security Settings</h3>
                    <form method="POST">
                        <div class="form-group">
                            <label>Current Password <span style="color:var(--danger)">*</span></label>
                            <input type="password" name="current_password" class="admin-input" required autocomplete="new-password">
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" name="new_password" class="admin-input" required minlength="6" autocomplete="new-password">
                            </div>
                            <div class="form-group">
                                <label>Confirm New Password</label>
                                <input type="password" name="confirm_password" class="admin-input" required minlength="6" autocomplete="new-password">
                            </div>
                        </div>
                        <button type="submit" name="update_password" class="btn-primary" style="background: var(--danger); color: white;">Change Password</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div id="adminToast" class="admin-toast">
        <i class="fa-solid fa-circle-check toast-icon"></i>
        <span class="toast-msg">Operation Successful!</span>
    </div>

    <script>
        // Handle URL Status Parameters for Toast
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        const toast = document.getElementById("adminToast");
        const msg = toast.querySelector(".toast-msg");
        const icon = toast.querySelector(".toast-icon");
        let isError = false;

        if (status) {
            switch(status) {
                case 'profile_updated': msg.innerText = "Profile information updated successfully!"; break;
                case 'avatar_removed': msg.innerText = "Profile image removed."; icon.className="fa-solid fa-trash toast-icon"; break;
                case 'password_updated': msg.innerText = "Security credentials updated successfully!"; break;
                case 'pass_error_current': msg.innerText = "Error: Current password is incorrect."; isError = true; break;
                case 'pass_error_match': msg.innerText = "Error: New passwords do not match."; isError = true; break;
                case 'pass_error_short': msg.innerText = "Error: Password must be at least 6 characters."; isError = true; break;
            }

            if (isError) {
                toast.classList.add("delete"); // Reusing the 'delete' red style for errors
                icon.className = "fa-solid fa-triangle-exclamation toast-icon";
            } else {
                toast.classList.remove("delete");
                if (!status.includes('removed')) icon.className = "fa-solid fa-circle-check toast-icon";
            }

            setTimeout(() => { toast.classList.add("show"); }, 200);
            setTimeout(() => { 
                toast.classList.remove("show"); 
                window.history.replaceState(null, null, window.location.pathname);
            }, 4000);
        }
    </script>
</body>
</html>