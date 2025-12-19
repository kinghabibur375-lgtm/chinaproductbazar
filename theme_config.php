<?php
// CRASH-PROOF CONFIGURATION
if (!isset($pdo)) { require 'config.php'; }

$theme = [];

// 1. Try to fetch from Database
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM theme_settings");
    while ($row = $stmt->fetch()) {
        $theme[$row['setting_key']] = $row['setting_value'];
    }
} catch (Exception $e) {
    // If DB fails, script continues with defaults below
}

// 2. Define Safe Defaults (Prevents White Screen)
$defaults = [
    'primary_color' => '#00e5ff',
    'secondary_color' => '#00acc1',
    'bg_body' => '#0b0f19',
    'bg_card' => '#151a27',
    'text_main' => '#ffffff',
    'text_heading' => '#ffffff',
    'border_color' => '#2a3b55',
    'font_family' => 'Poppins',
    'logo_width' => '150px'
];

// 3. Merge Defaults (Fill in missing gaps)
foreach ($defaults as $key => $val) {
    if (empty($theme[$key])) { $theme[$key] = $val; }
}

// 4. CSS Generator Function
function renderThemeCSS($t) {
    return ":root {
        --primary: {$t['primary_color']};
        --bg-body: {$t['bg_body']};
        --bg-card: {$t['bg_card']};
        --text-main: {$t['text_main']};
        --text-heading: {$t['text_heading']};
        --border: {$t['border_color']};
        --font-main: '{$t['font_family']}', sans-serif;
        --logo-width: {$t['logo_width']};
    }";
}
?>