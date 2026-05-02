<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/settings.php';
$page = 'blog';
$pageTitle = 'Blog';

$posts = db()->query("SELECT * FROM posts WHERE is_published = 1 ORDER BY published_at DESC")->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="wrap">
    <span class="eyebrow">Writings · Reflections · Prophecy</span>
    <h1>Blog</h1>
  </div>
</section>

<section class="block">
  <div class="wrap">
    <?php if ($posts): ?>
      <div class="card-grid">
        <?php foreach ($posts as $p): ?>
          <a class="card" href="<?= BASE_URL ?>/post.php?slug=<?= urlencode($p['slug']) ?>">
            <div class="card-img" style="background-image:url('<?= $p['featured_image'] ? UPLOAD_URL . '/' . e($p['featured_image']) : '' ?>');"></div>
            <div class="card-body">
              <div class="card-meta"><?= e(format_date($p['published_at'], 'M j, Y')) ?></div>
              <h3><?= e($p['title']) ?></h3>
              <p><?= e($p['excerpt'] ?: mb_strimwidth(strip_tags($p['body']), 0, 130, '…')) ?></p>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="section-head">
        <h2>No Posts Yet</h2>
        <p class="muted">Writings from the prophet's desk will appear here soon.</p>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
