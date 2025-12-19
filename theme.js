/* theme.js - v2.0 - Fixed Logic */

// 1. Immediate Apply (Prevents White Flash)
(function() {
    const savedTheme = localStorage.getItem('theme');
    // Only set attribute if user specifically requested light mode
    if (savedTheme === 'light') {
        document.documentElement.setAttribute('data-theme', 'light');
    } else {
        document.documentElement.removeAttribute('data-theme');
    }
})();

document.addEventListener('DOMContentLoaded', function() {
    updateVisuals();
});

// 2. Central Update Function
function updateVisuals() {
    const icon = document.getElementById('themeIcon');
    const logo = document.getElementById('siteLogo');
    const currentTheme = document.documentElement.getAttribute('data-theme') === 'light' ? 'light' : 'dark';

    // Update Icon
    if (icon) {
        if (currentTheme === 'light') {
            icon.className = 'fa-solid fa-moon'; // Moon icon for Light Mode (to switch to dark)
        } else {
            icon.className = 'fa-solid fa-sun'; // Sun icon for Dark Mode (to switch to light)
        }
    }

    // Update Logo (Uses variables from header.php)
    if (logo && typeof logoLight !== 'undefined' && typeof logoDark !== 'undefined') {
        logo.src = (currentTheme === 'light' && logoLight) ? logoLight : (logoDark);
    }
}

// 3. Toggle Action
function toggleTheme() {
    const html = document.documentElement;
    
    if (html.getAttribute('data-theme') === 'light') {
        // GO DARK
        html.removeAttribute('data-theme');
        localStorage.setItem('theme', 'dark');
    } else {
        // GO LIGHT
        html.setAttribute('data-theme', 'light');
        localStorage.setItem('theme', 'light');
    }
    
    updateVisuals();
}

function toggleMenu() {
    const nav = document.getElementById("mobileNav");
    if(nav) nav.classList.toggle("active");
}