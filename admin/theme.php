<?php
require_once __DIR__ . '/_layout.php';
require_login();

$ok = '';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) {
        $err = 'Security token expired.';
    } else {
        $picked = $_POST['theme'] ?? '';
        if (array_key_exists($picked, theme_choices())) {
            setting_set('theme', $picked);
            $ok = 'Theme updated. Refresh the public site to see changes.';
        } else {
            $err = 'Invalid theme.';
        }
    }
}

$current = active_theme();

admin_header('theme', 'Theme');
?>
<div class="admin-bar"><h1 style="margin:0;">Theme</h1></div>
<p class="muted" style="margin-bottom:2rem;">Choose the colour scheme for the public website.</p>

<?php if ($ok):  ?><div class="alert success"><?= e($ok) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert error"><?= e($err) ?></div><?php endif; ?>

<form method="post" style="max-width:900px;">
  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

  <div class="theme-grid">
    <!-- Light Green -->
    <label class="theme-card <?= $current==='light-green'?'active':'' ?>" for="theme-lg" data-theme="light-green">
      <div class="swatch" style="background:#f3faf4;border:1px solid #d6e6dc;">
        <span style="background:#ffffff;"></span>
        <span style="background:#7cc995;"></span>
        <span style="background:#4ea96b;"></span>
        <span style="background:#2f7a4a;"></span>
      </div>
      <div><input type="radio" id="theme-lg" name="theme" value="light-green" <?= $current==='light-green'?'checked':'' ?>>Light Green</div>
      <small class="muted">Bright, fresh, welcoming.</small>
    </label>

    <!-- Dark Gold -->
    <label class="theme-card <?= $current==='dark-gold'?'active':'' ?>" for="theme-dg" data-theme="dark-gold">
      <div class="swatch" style="background:#0b0b0e;">
        <span style="background:#14141a;"></span>
        <span style="background:#1c1c25;"></span>
        <span style="background:#d4a93b;"></span>
        <span style="background:#f0c75e;"></span>
      </div>
      <div><input type="radio" id="theme-dg" name="theme" value="dark-gold" <?= $current==='dark-gold'?'checked':'' ?>>Dark Gold</div>
      <small class="muted">Classic, regal, prophetic.</small>
    </label>

    <!-- Royal Purple -->
    <label class="theme-card <?= $current==='royal-purple'?'active':'' ?>" for="theme-rp" data-theme="royal-purple">
      <div class="swatch" style="background:#faf7ff;border:1px solid #ddd0f2;">
        <span style="background:#e3d8fb;"></span>
        <span style="background:#9a6dd6;"></span>
        <span style="background:#6b3fa0;"></span>
        <span style="background:#4b2778;"></span>
      </div>
      <div><input type="radio" id="theme-rp" name="theme" value="royal-purple" <?= $current==='royal-purple'?'checked':'' ?>>Royal Purple</div>
      <small class="muted">Royal, majestic, anointed.</small>
    </label>
  </div>

  <button class="btn btn-primary" type="submit">Save Theme</button>
</form>

<script>
document.querySelectorAll('.theme-card input[type=radio]').forEach(r => {
  r.addEventListener('change', () => {
    document.querySelectorAll('.theme-card').forEach(c => c.classList.remove('active'));
    r.closest('.theme-card').classList.add('active');
  });
});
</script>

<?php admin_footer(); ?>
