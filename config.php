<?php
// Database Credentials
$host = 'localhost';
$db   = 'royaldig_chinabazar_db';
// I added the prefix 'royaldig_' to the user. If your cPanel user is really just 'chinabazar_db', remove the prefix below.
$user = 'royaldig_chinabazar_db'; 
$pass = 'Z8PHxk5hhNttXX5';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Error handling
    echo "<h1>Database Connection Failed</h1>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p><strong>Check:</strong> Is the database user <code>royaldig_chinabazar_db</code> or just <code>chinabazar_db</code>?</p>";
    exit;
}

session_start();

// Fetch Site Settings
try {
    $stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
    $siteSettings = $stmt->fetch();
} catch (Exception $e) {
    // Fail silently if table not found (before import)
    $siteSettings = ['site_name' => 'China Product Bazar', 'gtm_id' => '', 'fb_pixel_id' => '', 'admin_email' => ''];
}
?>