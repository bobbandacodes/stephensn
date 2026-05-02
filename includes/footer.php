</main>
<footer class="site-footer">
  <div class="wrap footer-grid">
    <div>
      <h4><?= e(setting('site_name', SITE_NAME)) ?></h4>
      <p class="muted"><?= e(setting('footer_tagline', setting('site_tagline', SITE_TAGLINE))) ?></p>
    </div>
    <div>
      <h4><?= e(setting('ministry_title', 'Higher Life Church')) ?></h4>
      <p class="muted">
        <?= e(setting('church_day', 'Sunday')) ?> · <?= e(setting('church_time', '8:00 AM')) ?><br>
        <?= e(setting('church_venue', 'KPF Grand Hall, Parklands')) ?>
      </p>
    </div>
    <div>
      <h4>Connect</h4>
      <p class="muted">
        <?php if ($fb = setting('facebook_url')): ?><a href="<?= e($fb) ?>" target="_blank" rel="noopener">Facebook</a><br><?php endif; ?>
        <?php if ($yt = setting('youtube_url')): ?><a href="<?= e($yt) ?>" target="_blank" rel="noopener">YouTube</a><br><?php endif; ?>
        <?php if ($ig = setting('instagram_url')): ?><a href="<?= e($ig) ?>" target="_blank" rel="noopener">Instagram</a><br><?php endif; ?>
        <a href="<?= BASE_URL ?>/contact.php">Contact</a>
      </p>
    </div>
    <div>
      <h4>Explore</h4>
      <p class="muted">
        <a href="<?= BASE_URL ?>/blog.php">Blog</a><br>
        <a href="<?= BASE_URL ?>/gallery.php">Gallery</a><br>
        <a href="<?= BASE_URL ?>/give.php">Give</a>
      </p>
    </div>
  </div>
  <div class="wrap footer-bottom">
    <small>&copy; <?= date('Y') ?> <?= e(setting('site_name', SITE_NAME)) ?>. All rights reserved.</small>
  </div>
</footer>
<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
