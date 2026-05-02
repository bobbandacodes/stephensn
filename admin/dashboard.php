<?php
require_once __DIR__ . '/_layout.php';
require_login();

$stats = [
    'posts'    => (int)db()->query("SELECT COUNT(*) FROM posts WHERE is_published = 1")->fetchColumn(),
    'upcoming' => (int)db()->query("SELECT COUNT(*) FROM events WHERE event_date >= NOW()")->fetchColumn(),
    'sermons'  => (int)db()->query("SELECT COUNT(*) FROM sermons")->fetchColumn(),
    'gallery'  => (int)db()->query("SELECT COUNT(*) FROM gallery")->fetchColumn(),
    'unread'   => (int)db()->query("SELECT COUNT(*) FROM messages WHERE is_read = 0")->fetchColumn(),
];

admin_header('dashboard', 'Dashboard');
?>
<div class="admin-bar">
  <h1 style="margin:0;">Dashboard</h1>
  <span class="muted">Welcome, <?= e($_SESSION['admin_username'] ?? 'admin') ?> · Theme: <strong><?= e(theme_choices()[active_theme()] ?? active_theme()) ?></strong></span>
</div>

<div class="card-grid" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr));">
  <div class="card"><div class="card-body">
    <div class="card-meta">Published Posts</div>
    <h3 style="font-size:2.4rem;color:var(--accent-3);"><?= $stats['posts'] ?></h3>
  </div></div>
  <div class="card"><div class="card-body">
    <div class="card-meta">Upcoming Events</div>
    <h3 style="font-size:2.4rem;color:var(--accent-3);"><?= $stats['upcoming'] ?></h3>
  </div></div>
  <div class="card"><div class="card-body">
    <div class="card-meta">Sermons</div>
    <h3 style="font-size:2.4rem;color:var(--accent-3);"><?= $stats['sermons'] ?></h3>
  </div></div>
  <div class="card"><div class="card-body">
    <div class="card-meta">Gallery Photos</div>
    <h3 style="font-size:2.4rem;color:var(--accent-3);"><?= $stats['gallery'] ?></h3>
  </div></div>
  <div class="card"><div class="card-body">
    <div class="card-meta">Unread Messages</div>
    <h3 style="font-size:2.4rem;color:var(--accent-3);"><?= $stats['unread'] ?></h3>
  </div></div>
</div>

<div style="margin-top:3rem;">
  <h2>Quick Actions</h2>
  <p>
    <a href="<?= BASE_URL ?>/admin/posts.php?action=new" class="btn btn-primary">+ Write Blog Post</a>
    <a href="<?= BASE_URL ?>/admin/events.php?action=new" class="btn btn-ghost">+ New Event</a>
    <a href="<?= BASE_URL ?>/admin/sermons.php?action=new" class="btn btn-ghost">+ New Sermon</a>
    <a href="<?= BASE_URL ?>/admin/gallery.php" class="btn btn-ghost">+ Add Photos</a>
  </p>
  <p style="margin-top:1.5rem;">
    <a href="<?= BASE_URL ?>/admin/settings.php" class="btn btn-ghost">Edit Site Content &amp; Photos</a>
    <a href="<?= BASE_URL ?>/admin/theme.php" class="btn btn-ghost">Change Theme</a>
  </p>
</div>
<?php admin_footer(); ?>
