<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/settings.php';
$page = 'about';
$pageTitle = 'About';

$aboutImg = setting_image_url('about_image');
$gallery = db()->prepare("SELECT * FROM gallery WHERE category = ? ORDER BY sort_order ASC, created_at DESC LIMIT 8");
$gallery->execute(['stephen']);
$photos = $gallery->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<section class="page-hero <?= $aboutImg ? 'has-image' : '' ?>"
         <?= $aboutImg ? 'style="background-image:url(\'' . e($aboutImg) . '\');"' : '' ?>>
  <div class="wrap">
    <span class="eyebrow">About</span>
    <h1><?= e(setting('site_name', SITE_NAME)) ?></h1>
    <p class="muted"><?= e(setting('site_tagline', SITE_TAGLINE)) ?></p>
  </div>
</section>

<section class="block">
  <div class="wrap">
    <div class="row-split">
      <?php if ($aboutImg): ?>
        <img class="feature-img" src="<?= e($aboutImg) ?>" alt="">
      <?php else: ?>
        <div class="image-placeholder">Portrait — uploadable from admin</div>
      <?php endif; ?>
      <div>
        <h2><?= e(setting('about_title', 'The Calling')) ?></h2>
        <div style="white-space:pre-line;"><?= e(setting('about_body', '')) ?></div>
      </div>
    </div>
  </div>
</section>

<?php if ($photos): ?>
<section class="block alt">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow">In Ministry</span>
      <h2>Photo Gallery</h2>
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
