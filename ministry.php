<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/settings.php';
$page = 'ministry';
$pageTitle = 'Ministry';

$ministryImg = setting_image_url('ministry_image');
$gallery = db()->prepare("SELECT * FROM gallery WHERE category = ? ORDER BY sort_order ASC, created_at DESC LIMIT 8");
$gallery->execute(['church']);
$photos = $gallery->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<section class="page-hero <?= $ministryImg ? 'has-image' : '' ?>"
         <?= $ministryImg ? 'style="background-image:url(\'' . e($ministryImg) . '\');"' : '' ?>>
  <div class="wrap">
    <span class="eyebrow">The House</span>
    <h1><?= e(setting('ministry_title', 'Higher Life Church')) ?></h1>
    <p class="muted">Pastored by <?= e(setting('site_name', SITE_NAME)) ?></p>
  </div>
</section>

<section class="block">
  <div class="wrap">
    <div class="church-card">
      <h2>Join Us in Worship</h2>
      <div class="church-meta">
        <div><div class="label">Day</div><div><?= e(setting('church_day', 'Sunday')) ?></div></div>
        <div><div class="label">Time</div><div><?= e(setting('church_time', '8:00 AM')) ?></div></div>
        <div><div class="label">Venue</div><div><?= e(setting('church_venue', 'KPF Grand Hall, Parklands')) ?></div></div>
      </div>
    </div>
  </div>
</section>

<section class="block alt">
  <div class="wrap">
    <div class="row-split">
      <?php if ($ministryImg): ?>
        <img class="feature-img" src="<?= e($ministryImg) ?>" alt="">
      <?php else: ?>
        <div class="image-placeholder">Church photo — uploadable from admin</div>
      <?php endif; ?>
      <div>
        <h2>Our Heart</h2>
        <div style="white-space:pre-line;"><?= e(setting('ministry_body', '')) ?></div>
        <p style="margin-top:2rem;"><a href="<?= BASE_URL ?>/contact.php" class="btn btn-ghost">Plan Your Visit</a></p>
      </div>
    </div>
  </div>
</section>

<?php if ($photos): ?>
<section class="block">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow">Our Family</span>
      <h2>Church Gallery</h2>
    </div>
    <div class="gallery-grid">
      <?php foreach ($photos as $g): ?>
        <div class="gallery-item"
             data-full="<?= UPLOAD_URL . '/' . e($g['image']) ?>"
             style="background-image:url('<?= UPLOAD_URL . '/' . e($g['image']) ?>');">
          <?php if ($g['caption']): ?><div class="caption"><?= e($g['caption']) ?></div><?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
