<?php
require_once __DIR__ . '/includes/db.php';
$page = 'media';
$pageTitle = 'Media';

$sermons = db()->query("SELECT * FROM sermons ORDER BY COALESCE(preached_on, created_at) DESC")->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="wrap">
    <span class="eyebrow">Watch &amp; Listen</span>
    <h1>Sermons &amp; Teachings</h1>
  </div>
</section>

<section class="block">
  <div class="wrap">
    <?php if ($sermons): ?>
      <div class="card-grid">
        <?php foreach ($sermons as $s): ?>
          <a class="card" href="<?= e($s['video_url'] ?: '#') ?>" target="_blank" rel="noopener">
            <div class="card-img" style="background-image:url('<?= $s['thumbnail'] ? UPLOAD_URL . '/' . e($s['thumbnail']) : '' ?>');"></div>
            <div class="card-body">
              <div class="card-meta"><?= e(format_date($s['preached_on'] ?? $s['created_at'], 'M j, Y')) ?></div>
              <h3><?= e($s['title']) ?></h3>
              <p><?= e(mb_strimwidth(strip_tags((string)$s['description']), 0, 130, '…')) ?></p>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="section-head">
        <h2>Coming Soon</h2>
        <p class="muted">Recorded messages will appear here. Subscribe to our channels to be notified.</p>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
