<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/settings.php';
$page = 'give';
$pageTitle = 'Give';
$giveImg = setting_image_url('give_image');
include __DIR__ . '/includes/header.php';
?>

<section class="page-hero <?= $giveImg ? 'has-image' : '' ?>"
         <?= $giveImg ? 'style="background-image:url(\'' . e($giveImg) . '\');"' : '' ?>>
  <div class="wrap">
    <span class="eyebrow">Partnership</span>
    <h1>Give</h1>
    <p class="muted">Sow into the vision and the mantle of this ministry.</p>
  </div>
</section>

<section class="block">
  <div class="wrap">
    <div class="church-card coming-soon">
      <span class="badge">Coming Soon</span>
      <h2>Online Giving Launching Shortly</h2>
      <p class="muted" style="max-width:560px;margin:1rem auto 0;">
        <?= e(setting('give_message', '')) ?>
        Please <a href="<?= BASE_URL ?>/contact.php">contact us</a> for partnership details.
      </p>
      <p class="muted" style="margin-top:2rem;font-size:.9rem;font-style:italic;">
        2 Corinthians 9:7 — "Each one must give as he has decided in his heart, not reluctantly or under compulsion, for God loves a cheerful giver."
      </p>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
