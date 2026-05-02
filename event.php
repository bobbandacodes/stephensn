<?php
require_once __DIR__ . '/includes/db.php';
$page = 'events';

$slug = $_GET['slug'] ?? '';
$stmt = db()->prepare("SELECT * FROM events WHERE slug = ? LIMIT 1");
$stmt->execute([$slug]);
$ev = $stmt->fetch();

if (!$ev) {
    http_response_code(404);
    $pageTitle = 'Event not found';
    include __DIR__ . '/includes/header.php';
    echo '<section class="block"><div class="wrap"><h1>Event not found</h1><p class="muted">This event may have been removed.</p><p><a href="' . BASE_URL . '/events.php" class="btn btn-ghost">Back to events</a></p></div></section>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $ev['title'];
include __DIR__ . '/includes/header.php';
?>

<section class="page-hero" <?= $ev['image'] ? 'style="background-image:linear-gradient(180deg,rgba(11,11,14,.7),var(--bg)),url(\'' . UPLOAD_URL . '/' . e($ev['image']) . '\');background-size:cover;background-position:center;"' : '' ?>>
  <div class="wrap">
    <span class="eyebrow"><?= e(format_date($ev['event_date'])) ?></span>
    <h1><?= e($ev['title']) ?></h1>
    <?php if ($ev['location']): ?><p class="muted">📍 <?= e($ev['location']) ?></p><?php endif; ?>
  </div>
</section>

<section class="block">
  <div class="wrap" style="max-width:780px;">
    <div style="white-space:pre-line;"><?= e($ev['description']) ?></div>

    <?php if ($ev['register_url']): ?>
      <div style="margin-top:2.5rem;">
        <a class="btn btn-primary" href="<?= e($ev['register_url']) ?>" target="_blank" rel="noopener">Register</a>
      </div>
    <?php endif; ?>

    <p style="margin-top:3rem;"><a href="<?= BASE_URL ?>/events.php" class="btn btn-ghost">← All Events</a></p>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
