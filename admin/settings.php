<?php
require_once __DIR__ . '/_layout.php';
require_once __DIR__ . '/../includes/upload.php';
require_login();

// Map of slot key => human label, type ('text'|'textarea'|'image'|'url')
$slots = [
    // Identity
    'site_name'      => ['label' => 'Site / Prophet Name',  'type' => 'text'],
    'site_tagline'   => ['label' => 'Tagline',              'type' => 'text'],
    'footer_tagline' => ['label' => 'Footer Tagline',       'type' => 'text'],
    'site_logo'      => ['label' => 'Site Logo',            'type' => 'image'],
    'site_logo_dark' => ['label' => 'Site Logo (Dark Theme)', 'type' => 'image'],

    // Hero
    'hero_eyebrow'   => ['label' => 'Hero Eyebrow (small text above title)', 'type' => 'text'],
    'hero_title'     => ['label' => 'Hero Title (use new lines for breaks)', 'type' => 'textarea'],
    'hero_subtitle'  => ['label' => 'Hero Subtitle',        'type' => 'textarea'],
    'hero_image'     => ['label' => 'Hero Background Image','type' => 'image'],

    // About
    'about_title'    => ['label' => 'About Section Title',  'type' => 'text'],
    'about_body'     => ['label' => 'About Body',           'type' => 'textarea'],
    'about_image'    => ['label' => 'About / Portrait Photo','type' => 'image'],

    // Ministry / Church
    'ministry_title' => ['label' => 'Church Name',          'type' => 'text'],
    'ministry_body'  => ['label' => 'Ministry Body',        'type' => 'textarea'],
    'ministry_image' => ['label' => 'Church Photo',         'type' => 'image'],
    'church_day'     => ['label' => 'Service Day',          'type' => 'text'],
    'church_time'    => ['label' => 'Service Time',         'type' => 'text'],
    'church_venue'   => ['label' => 'Venue',                'type' => 'text'],

    // Give
    'give_message'   => ['label' => 'Give Page Message',    'type' => 'textarea'],
    'give_image'     => ['label' => 'Give Page Image',      'type' => 'image'],

    // Contact / socials
    'contact_email'  => ['label' => 'Contact Email',        'type' => 'text'],
    'contact_phone'  => ['label' => 'Contact Phone',        'type' => 'text'],
    'facebook_url'   => ['label' => 'Facebook URL',         'type' => 'url'],
    'youtube_url'    => ['label' => 'YouTube URL',          'type' => 'url'],
    'instagram_url'  => ['label' => 'Instagram URL',        'type' => 'url'],
];

$ok = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) {
        $err = 'Security token expired.';
    } else {
        // Save text fields
        foreach ($slots as $key => $meta) {
            if ($meta['type'] === 'image') continue;
            if (array_key_exists($key, $_POST)) {
                setting_set($key, trim((string)$_POST[$key]));
            }
        }

        // Handle "remove image" checkboxes
        foreach ($slots as $key => $meta) {
            if ($meta['type'] !== 'image') continue;
            if (!empty($_POST['remove_' . $key])) {
                delete_upload(setting($key));
                setting_set($key, '');
            }
        }

        // Handle uploads
        foreach ($slots as $key => $meta) {
            if ($meta['type'] !== 'image') continue;
            if (empty($_FILES[$key]['name'])) continue;
            $up = handle_image_upload($_FILES[$key]);
            if ($up['error']) { $err = "$key: " . $up['error']; break; }
            if ($up['filename']) {
                delete_upload(setting($key));
                setting_set($key, $up['filename']);
            }
        }

        if (!$err) {
            $ok = 'Settings saved.';
        }
    }
}

admin_header('settings', 'Site Settings');
?>
<div class="admin-bar">
  <h1 style="margin:0;">Site Settings</h1>
  <a href="<?= BASE_URL ?>/index.php" target="_blank" class="btn btn-ghost">↗ View Site</a>
</div>
<p class="muted" style="margin-bottom:2rem;">Edit copy and replace photos shown across the public website.</p>

<?php if ($ok):  ?><div class="alert success"><?= e($ok) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert error"><?= e($err) ?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data" class="form" style="max-width:760px;margin:0;">
  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

  <?php
  $sections = [
    'Identity'  => ['site_name','site_tagline','footer_tagline','site_logo','site_logo_dark'],
    'Home Hero' => ['hero_eyebrow','hero_title','hero_subtitle','hero_image'],
    'About'     => ['about_title','about_body','about_image'],
    'Ministry / Church' => ['ministry_title','ministry_body','ministry_image','church_day','church_time','church_venue'],
    'Give'      => ['give_message','give_image'],
    'Contact &amp; Social' => ['contact_email','contact_phone','facebook_url','youtube_url','instagram_url'],
  ];
  foreach ($sections as $title => $keys): ?>
    <h2 style="margin-top:2.5rem;border-bottom:1px solid var(--line);padding-bottom:.5rem;"><?= $title ?></h2>
    <?php foreach ($keys as $key):
        $meta = $slots[$key];
        $val  = setting($key, '');
    ?>
      <?php if ($meta['type'] === 'image'): ?>
        <div class="image-slot">
          <label><?= e($meta['label']) ?></label>
          <div style="display:flex;gap:1.25rem;align-items:flex-start;flex-wrap:wrap;">
            <?php if ($val): ?>
              <img class="thumb" src="<?= UPLOAD_URL . '/' . e($val) ?>" alt="">
            <?php else: ?>
              <div class="thumb" style="display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:.8rem;">No image</div>
            <?php endif; ?>
            <div style="flex:1;min-width:240px;">
              <input type="file" name="<?= e($key) ?>" accept="image/*">
              <?php if ($val): ?>
                <p style="margin:.6rem 0 0;font-size:.85rem;">
                  <label><input type="checkbox" name="remove_<?= e($key) ?>"> Remove current image</label>
                </p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php elseif ($meta['type'] === 'textarea'): ?>
        <div class="field">
          <label><?= e($meta['label']) ?></label>
          <textarea name="<?= e($key) ?>" rows="5"><?= e($val) ?></textarea>
        </div>
      <?php else: ?>
        <div class="field">
          <label><?= e($meta['label']) ?></label>
          <input type="<?= $meta['type'] === 'url' ? 'url' : 'text' ?>" name="<?= e($key) ?>" value="<?= e($val) ?>">
        </div>
      <?php endif; ?>
    <?php endforeach; ?>
  <?php endforeach; ?>

  <div style="margin-top:2.5rem;">
    <button class="btn btn-primary" type="submit">Save All Settings</button>
  </div>
</form>

<?php admin_footer(); ?>
