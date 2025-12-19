<?php
class Theme {
    private $pdo;
    private $settings = [];

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->loadSettings();
    }

    private function loadSettings() {
        try {
            $stmt = $this->pdo->query("SELECT setting_key, setting_value FROM theme_settings");
            while ($row = $stmt->fetch()) { $this->settings[$row['setting_key']] = $row['setting_value']; }
        } catch (Exception $e) {}
    }

    public function get($key, $default = '') { return $this->settings[$key] ?? $default; }

    public function getLogo($mode) {
        $key = ($mode == 'light') ? 'logo_light' : 'logo_dark';
        return !empty($this->settings[$key]) ? 'uploads/'.$this->settings[$key] : 'uploads/logo.png';
    }

    public function renderCSS() {
        // Defaults to prevent white screen
        $p = $this->get('primary', '#00e5ff');
        $bb = $this->get('bg_body', '#f4f6f8'); $dbb = $this->get('dm_bg_body', '#0b0f19');
        $bc = $this->get('bg_card', '#ffffff'); $dbc = $this->get('dm_bg_card', '#151a27');
        $tm = $this->get('text_main', '#1a202c'); $dtm = $this->get('dm_text_main', '#ffffff');
        $bd = $this->get('border', '#e2e8f0'); $dbd = $this->get('dm_border', '#2d3748');
        
        return "
        :root {
            --primary: $p;
            --bg-body: $dbb; --bg-card: $dbc; --text-main: $dtm; --border: $dbd; /* Default Dark */
            --font-head: '{$this->get('font_head', 'Rajdhani')}', sans-serif;
            --font-body: '{$this->get('font_body', 'Poppins')}', sans-serif;
            --radius: {$this->get('radius', '8px')};
            --logo-w: {$this->get('logo_width', '150px')};
        }
        [data-theme='light'] {
            --bg-body: $bb; --bg-card: $bc; --text-main: $tm; --border: $bd;
        }";
    }
}
?>