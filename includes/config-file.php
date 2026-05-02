<?php
// Site config
define('SITE_NAME', "Prophet Stephen SN");
define('SITE_TAGLINE', "The Paragon");
define('CHURCH_NAME', "Higher Life Church");
define('CHURCH_VENUE', "KPF Grand Hall, Parklands");
define('CHURCH_DAY', "Sunday");
define('CHURCH_TIME', "8:00 AM");
define('CONTACT_EMAIL', "info@stephensn.local");

// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'u763598602_stephensn');
define('DB_USER', 'u763598602_stephensn');
define('DB_PASS', 'Stephen_SN_2026');
define('DB_CHARSET', 'utf8mb4');

// Paths / URLs
define('BASE_URL', '/https://plum-gaur-348103.hostingersite.com/');
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
