<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/settings.php';
$page = $page ?? '';
$siteName = setting('site_name', SITE_NAME);
$siteTagline = setting('site_tagline', SITE_TAGLINE);
$pageTitle = isset($pageTitle) ? $pageTitle . ' — ' . $siteName : $siteName . ' · ' . $siteTagline;
$siteLogo = setting_image_url('site_logo');
$siteLogoDark = setting_image_url('site_logo_dark');
$theme = active_theme();
$nav = [
    'home'     => ['Home',     '/index.php'],
    'about'    => ['About',    '/about.php'],
    'ministry' => ['Ministry', '/ministry.php'],
    'events'   => ['Events',   '/events.php'],
    'media'    => ['Media',    '/media.php'],
    'blog'     => ['Blog',     '/blog.php'],
    'gallery'  => ['Gallery',  '/gallery.php'],
    'give'     => ['Give',     '/give.php'],
];
?><!DOCTYPE html>
<html lang="en" data-theme="<?= e($theme) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e($siteName . ' — ' . $siteTagline . '. ' . setting('ministry_title', 'Higher Life Church') . '.') ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="page-<?= e($page) ?>">
<header class="site-header">
  <div class="wrap">
    <a class="brand" href="<?= BASE_URL ?>/index.php">
      <?php if ($siteLogo): ?>
        <img src="<?= e($siteLogo) ?>" alt="<?= e($siteName) ?>" class="brand-logo">
      <?php else: ?>
        <span class="brand-name"><?= e($siteName) ?></span>
      <?php endif; ?>
      <span class="brand-tagline"><?= e($siteTagline) ?></span>
    </a>

    <nav class="site-nav">
      <?php foreach ($nav as $key => [$label, $href]): ?>
        <a href="<?= BASE_URL . $href ?>" class="<?= $page===$key?'active':'' ?>"><?= e($label) ?></a>
      <?php endforeach; ?>
      <a href="<?= BASE_URL ?>/contact.php" class="cta <?= $page==='contact'?'active':'' ?>">Contact</a>
    </nav>

    <button class="nav-toggle" aria-label="Open menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<!-- Mobile fullscreen menu -->
<div class="mobile-menu" aria-hidden="true">
  <div class="mobile-menu-inner">
    <nav>
      <?php foreach ($nav as $key => [$label, $href]): ?>
        <a href="<?= BASE_URL . $href ?>" class="<?= $page===$key?'active':'' ?>"><?= e($label) ?></a>
      <?php endforeach; ?>
      <a href="<?= BASE_URL ?>/contact.php" class="<?= $page==='contact'?'active':'' ?>">Contact</a>
    </nav>
    <div class="mobile-menu-foot">
      <p class="muted"><?= e($siteName) ?> · <?= e($siteTagline) ?></p>
      <p class="muted small">
        <?php if ($fb = setting('facebook_url')): ?><a href="<?= e($fb) ?>" target="_blank" rel="noopener">Facebook</a><?php endif; ?>
        <?php if ($yt = setting('youtube_url')): ?> · <a href="<?= e($yt) ?>" target="_blank" rel="noopener">YouTube</a><?php endif; ?>
      </p>
    </div>
  </div>
</div>

<main class="site-main">
