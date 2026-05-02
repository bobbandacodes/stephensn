<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/settings.php';
$page = 'home';
$pageTitle = null;

$events  = db()->query("SELECT * FROM events WHERE event_date >= NOW() ORDER BY event_date ASC LIMIT 3")->fetchAll();
$sermons = db()->query("SELECT * FROM sermons ORDER BY COALESCE(preached_on, created_at) DESC LIMIT 3")->fetchAll();
$posts   = db()->query("SELECT * FROM posts WHERE is_published = 1 ORDER BY published_at DESC LIMIT 3")->fetchAll();
$gallery = db()->query("SELECT * FROM gallery ORDER BY sort_order ASC, created_at DESC LIMIT 6")->fetchAll();

$heroImg = setting_image_url('hero_image');
$aboutImg = setting_image_url('about_image');
$ministryImg = setting_image_url('ministry_image');

include __DIR__ . '/includes/header.php';
?>

<section class="hero<?= $heroImg ? ' has-image' : '' ?>" <?= $heroImg ? 'style="background-image:url(\'' . e($heroImg) . '\');"' : '' ?>>
  <div class="hero-inner">
    <span class="eyebrow"><?= e(setting('hero_eyebrow', 'Welcome to the Ministry')) ?></span>
    <h1><?= nl2br(e(setting('hero_title', "Prophet Stephen SN\nA voice for this generation"))) ?></h1>
    <p class="lead"><?= e(setting('hero_subtitle', 'carrying the prophetic mantle, raising disciples, and pastoring Higher Life Church. Encounter God. Walk in identity. Manifest the Kingdom.')) ?></p>
    <div class="hero-cta">
      <a href="<?= BASE_URL ?>/events.php" class="btn btn-primary">Upcoming Events</a>
      <a href="<?= BASE_URL ?>/media.php" class="btn btn-ghost">Watch Latest</a>
    </div>
  </div>
</section>

<!-- About preview -->
<section class="block">
  <div class="wrap">
    <div class="row-split">
      <?php if ($aboutImg): ?>
        <img class="feature-img" src="<?= e($aboutImg) ?>" alt="<?= e(setting('site_name', SITE_NAME)) ?>">
      <?php else: ?>
        <div class="image-placeholder">Photo of Prophet Stephen</div>
      <?php endif; ?>
      <div>
        <span class="eyebrow">About</span>
        <h2><?= e(setting('about_title', 'The Calling')) ?></h2>
        <p><?= e(mb_strimwidth(setting('about_body', ''), 0, 360, '…')) ?></p>
        <p><a href="<?= BASE_URL ?>/about.php" class="btn btn-ghost">Read More</a></p>
      </div>
    </div>
  </div>
</section>

<!-- Ministry / Church -->
<section class="block alt">
  <div class="wrap">
    <div class="row-split reverse">
      <?php if ($ministryImg): ?>
        <img class="feature-img" src="<?= e($ministryImg) ?>" alt="<?= e(setting('ministry_title', 'Higher Life Church')) ?>">
      <?php else: ?>
        <div class="image-placeholder">Photo of the Church</div>
      <?php endif; ?>
      <div>
        <span class="eyebrow">Pastoring</span>
        <h2><?= e(setting('ministry_title', 'Higher Life Church')) ?></h2>
        <p>Join us in worship every <?= e(setting('church_day', 'Sunday')) ?>.</p>
        <div class="church-meta" style="justify-content:flex-start;">
          <div><div class="label">Day</div><div><?= e(setting('church_day', 'Sunday')) ?></div></div>
          <div><div class="label">Time</div><div><?= e(setting('church_time', '8:00 AM')) ?></div></div>
          <div><div class="label">Venue</div><div><?= e(setting('church_venue', 'KPF Grand Hall, Parklands')) ?></div></div>
        </div>
        <p style="margin-top:2rem;"><a href="<?= BASE_URL ?>/ministry.php" class="btn btn-ghost">Learn More</a></p>
      </div>
    </div>
  </div>
</section>

<?php if ($events): ?>
<section class="block">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow">Upcoming</span>
      <h2>Events &amp; Conferences</h2>
    </div>
    <div class="card-grid">
      <?php foreach ($events as $ev): ?>
        <a class="card" href="<?= BASE_URL ?>/event.php?slug=<?= urlencode($ev['slug']) ?>">
          <div class="card-img" style="background-image:url('<?= $ev['image'] ? UPLOAD_URL . '/' . e($ev['image']) : '' ?>');"></div>
          <div class="card-body">
            <div class="card-meta"><?= e(format_date($ev['event_date'])) ?></div>
            <h3><?= e($ev['title']) ?></h3>
            <p><?= e(mb_strimwidth(strip_tags((string)$ev['description']), 0, 110, '…')) ?></p>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
    <div style="text-align:center;margin-top:2.5rem;"><a href="<?= BASE_URL ?>/events.php" class="btn btn-ghost">All Events</a></div>
  </div>
</section>
<?php endif; ?>

<?php if ($posts): ?>
<section class="block alt">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow">From the Blog</span>
      <h2>Latest Writings</h2>
    </div>
    <div class="card-grid">
      <?php foreach ($posts as $p): ?>
        <a class="card" href="<?= BASE_URL ?>/post.php?slug=<?= urlencode($p['slug']) ?>">
          <div class="card-img" style="background-image:url('<?= $p['featured_image'] ? UPLOAD_URL . '/' . e($p['featured_image']) : '' ?>');"></div>
          <div class="card-body">
            <div class="card-meta"><?= e(format_date($p['published_at'], 'M j, Y')) ?></div>
            <h3><?= e($p['title']) ?></h3>
            <p><?= e($p['excerpt'] ?: mb_strimwidth(strip_tags($p['body']), 0, 110, '…')) ?></p>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
    <div style="text-align:center;margin-top:2.5rem;"><a href="<?= BASE_URL ?>/blog.php" class="btn btn-ghost">All Posts</a></div>
  </div>
</section>
<?php endif; ?>

<?php if ($sermons): ?>
<section class="block">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow">Watch &amp; Listen</span>
      <h2>Latest Messages</h2>
    </div>
    <div class="card-grid">
      <?php foreach ($sermons as $s): ?>
        <a class="card" href="<?= e($s['video_url'] ?: '#') ?>" target="_blank" rel="noopener">
          <div class="card-img" style="background-image:url('<?= $s['thumbnail'] ? UPLOAD_URL . '/' . e($s['thumbnail']) : '' ?>');"></div>
          <div class="card-body">
            <div class="card-meta"><?= e(format_date($s['preached_on'] ?? $s['created_at'], 'M j, Y')) ?></div>
            <h3><?= e($s['title']) ?></h3>
            <p><?= e(mb_strimwidth(strip_tags((string)$s['description']), 0, 110, '…')) ?></p>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
    <div style="text-align:center;margin-top:2.5rem;"><a href="<?= BASE_URL ?>/media.php" class="btn btn-ghost">All Messages</a></div>
  </div>
</section>
<?php endif; ?>

<?php if ($gallery): ?>
<section class="block alt">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow">Moments</span>
      <h2>Gallery</h2>
    </div>
    <div class="gallery-grid">
      <?php foreach ($gallery as $g): ?>
        <div class="gallery-item"
             data-full="<?= UPLOAD_URL . '/' . e($g['image']) ?>"
             style="background-image:url('<?= UPLOAD_URL . '/' . e($g['image']) ?>');">
          <?php if ($g['caption']): ?><div class="caption"><?= e($g['caption']) ?></div><?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
    <div style="text-align:center;margin-top:2.5rem;"><a href="<?= BASE_URL ?>/gallery.php" class="btn btn-ghost">View All</a></div>
  </div>
</section>
<?php endif; ?>

<section class="block">
  <div class="wrap" style="text-align:center;max-width:760px;">
    <span class="eyebrow">Partner</span>
    <h2>Sow into the Vision</h2>
    <p class="muted">The mantle on this ministry advances by the partnership of those who believe in the message. Stand with the prophet.</p>
    <a href="<?= BASE_URL ?>/give.php" class="btn btn-primary" style="margin-top:1rem;">Give</a>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
