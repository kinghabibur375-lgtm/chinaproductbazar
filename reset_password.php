<?php
// reset_password.php
require 'config.php';

// CHANGE THIS to your desired new password
$new_password = "MyNewSecurePassword123"; 

$hash = password_hash($new_password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE username = 'admin'");
$stmt->execute([$hash]);

echo "Password updated successfully! Delete this file now.";
?>