<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/settings.php';

function admin_header(string $page, string $title): void {
    $theme = active_theme();
    ?><!DOCTYPE html>
<html lang="en" data-theme="<?= e($theme) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title) ?> — Admin · <?= e(setting('site_name', SITE_NAME)) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="admin-shell">
  <aside class="admin-sidebar">
    <h3>Admin Console</h3>
    <a href="<?= BASE_URL ?>/admin/dashboard.php" class="<?= $page==='dashboard'?'active':'' ?>">Dashboard</a>
    <a href="<?= BASE_URL ?>/admin/settings.php" class="<?= $page==='settings'?'active':'' ?>">Site Settings</a>
    <a href="<?= BASE_URL ?>/admin/theme.php" class="<?= $page==='theme'?'active':'' ?>">Theme</a>
    <a href="<?= BASE_URL ?>/admin/posts.php" class="<?= $page==='posts'?'active':'' ?>">Blog Posts</a>
    <a href="<?= BASE_URL ?>/admin/events.php" class="<?= $page==='events'?'active':'' ?>">Events</a>
    <a href="<?= BASE_URL ?>/admin/sermons.php" class="<?= $page==='sermons'?'active':'' ?>">Sermons</a>
    <a href="<?= BASE_URL ?>/admin/gallery.php" class="<?= $page==='gallery'?'active':'' ?>">Gallery</a>
    <a href="<?= BASE_URL ?>/admin/messages.php" class="<?= $page==='messages'?'active':'' ?>">Messages</a>
    <a href="<?= BASE_URL ?>/admin/password.php" class="<?= $page==='password'?'active':'' ?>">Change Password</a>
    <hr style="border:0;border-top:1px solid var(--line);margin:1rem 0;">
    <a href="<?= BASE_URL ?>/index.php" target="_blank">↗ View Site</a>
    <a href="<?= BASE_URL ?>/admin/logout.php">Logout</a>
  </aside>
  <main class="admin-content">
    <?php
}

function admin_footer(): void {
    ?>
  </main>
</div>
<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
    <?php
}
