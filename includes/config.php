<?php
// Production configuration for Hostinger
// Copy this file to config.php when deploying to Hostinger

// Site config
define('SITE_NAME', "Prophet Stephen SN");
define('SITE_TAGLINE', "The Paragon");
define('CHURCH_NAME', "Higher Life Church");
define('CHURCH_VENUE', "KPF Grand Hall, Parklands");
define('CHURCH_DAY', "Sunday");
define('CHURCH_TIME', "8:00 AM");
define('CONTACT_EMAIL', "info@plum-gaur-348103.hostingersite.com");

// Database - Hostinger Settings
define('DB_HOST', 'localhost'); // Hostinger uses localhost
define('DB_NAME', 'u763598602_sn'); // Correct database name
define('DB_USER', 'u763598602_sn'); // Correct database user
define('DB_PASS', 'StephenSN_2026'); // Correct database password
define('DB_CHARSET', 'utf8mb4'); // Hostinger compatible charset

// Paths / URLs - Production
define('BASE_URL', 'https://plum-gaur-348103.hostingersite.com'); // Actual domain
define('UPLOAD_DIR', __DIR__ . '/../assets/uploads');
define('UPLOAD_URL', BASE_URL . '/assets/uploads');

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}
