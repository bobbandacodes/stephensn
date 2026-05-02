<?php
require_once __DIR__ . '/includes/db.php';
$page = 'events';
$pageTitle = 'Events';

$upcoming = db()->query("SELECT * FROM events WHERE event_date >= NOW() ORDER BY event_date ASC")->fetchAll();
$past = db()->query("SELECT * FROM events WHERE event_date < NOW() ORDER BY event_date DESC LIMIT 6")->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="wrap">
    <span class="eyebrow">Conferences · Services · Tours</span>
    <h1>Events</h1>
  </div>
</section>

<section class="block">
  <div class="wrap">
    <?php if ($upcoming): ?>
      <div class="section-head"><h2>Upcoming</h2></div>
      <div class="card-grid">
        <?php foreach ($upcoming as $ev): ?>
          <a class="card" href="<?= BASE_URL ?>/event.php?slug=<?= urlencode($ev['slug']) ?>">
            <div class="card-img" style="background-image:url('<?= $ev['image'] ? UPLOAD_URL . '/' . e($ev['image']) : '' ?>');"></div>
            <div class="card-body">
              <div class="card-meta"><?= e(format_date($ev['event_date'])) ?></div>
              <h3><?= e($ev['title']) ?></h3>
              <p><?= e(mb_strimwidth(strip_tags((string)$ev['description']), 0, 130, '…')) ?></p>
              <?php if ($ev['location']): ?><p class="muted" style="font-size:.85rem;">📍 <?= e($ev['location']) ?></p><?php endif; ?>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="section-head">
        <h2>No Upcoming Events</h2>
        <p class="muted">Check back soon — new dates are added regularly.</p>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php if ($past): ?>
<section class="block alt">
  <div class="wrap">
    <div class="section-head"><h2>Past Events</h2></div>
    <div class="card-grid">
      <?php foreach ($past as $ev): ?>
        <div class="card" style="opacity:.75;">
          <div class="card-img" style="background-image:url('<?= $ev['image'] ? UPLOAD_URL . '/' . e($ev['image']) : '' ?>');"></div>
          <div class="card-body">
            <div class="card-meta"><?= e(format_date($ev['event_date'])) ?></div>
            <h3><?= e($ev['title']) ?></h3>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
