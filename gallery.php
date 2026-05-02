<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/settings.php';
$page = 'gallery';
$pageTitle = 'Gallery';

$category = $_GET['cat'] ?? 'all';
$valid = ['all', 'general', 'stephen', 'church', 'events'];
if (!in_array($category, $valid, true)) $category = 'all';

if ($category === 'all') {
    $photos = db()->query("SELECT * FROM gallery ORDER BY sort_order ASC, created_at DESC")->fetchAll();
} else {
    $stmt = db()->prepare("SELECT * FROM gallery WHERE category = ? ORDER BY sort_order ASC, created_at DESC");
    $stmt->execute([$category]);
    $photos = $stmt->fetchAll();
}

include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="wrap">
    <span class="eyebrow">Moments &amp; Memories</span>
    <h1>Gallery</h1>
  </div>
</section>

<section class="block">
  <div class="wrap">
    <div style="display:flex;justify-content:center;gap:.6rem;flex-wrap:wrap;margin-bottom:2.5rem;">
      <?php foreach (['all'=>'All','stephen'=>'Prophet Stephen','church'=>'The Church','events'=>'Events','general'=>'Other'] as $k => $label): ?>
        <a class="btn <?= $category === $k ? 'btn-primary' : 'btn-ghost' ?>" style="padding:.5rem 1.2rem;font-size:.85rem;"
           href="?cat=<?= e($k) ?>"><?= e($label) ?></a>
      <?php endforeach; ?>
    </div>

    <?php if ($photos): ?>
      <div class="gallery-grid">
        <?php foreach ($photos as $g): ?>
          <div class="gallery-item"
               data-full="<?= UPLOAD_URL . '/' . e($g['image']) ?>"
               style="background-image:url('<?= UPLOAD_URL . '/' . e($g['image']) ?>');">
            <?php if ($g['caption']): ?><div class="caption"><?= e($g['caption']) ?></div><?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="section-head">
        <h2>Photos Coming Soon</h2>
        <p class="muted">Photos will be added shortly.</p>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
