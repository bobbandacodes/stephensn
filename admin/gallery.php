<?php
require_once __DIR__ . '/_layout.php';
require_once __DIR__ . '/../includes/upload.php';
require_login();

$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$err = '';
$ok = '';
$valid_categories = ['general' => 'Other', 'stephen' => 'Prophet Stephen', 'church' => 'The Church', 'events' => 'Events'];

if ($action === 'delete' && $id && csrf_check($_GET['csrf'] ?? '')) {
    $stmt = db()->prepare("SELECT image FROM gallery WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row) delete_upload($row['image']);
    db()->prepare("DELETE FROM gallery WHERE id = ?")->execute([$id]);
    redirect(BASE_URL . '/admin/gallery.php?msg=deleted');
}

// Bulk upload (multiple files)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'upload') {
    if (!csrf_check($_POST['csrf'] ?? '')) {
        $err = 'Security token expired.';
    } else {
        $category = $_POST['category'] ?? 'general';
        if (!array_key_exists($category, $valid_categories)) $category = 'general';
        $captionDefault = trim($_POST['caption'] ?? '');
        $count = 0;

        if (!empty($_FILES['photos']['name'][0])) {
            $files = $_FILES['photos'];
            $n = count($files['name']);
            for ($i = 0; $i < $n; $i++) {
                if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
                $single = [
                    'name'     => $files['name'][$i],
                    'type'     => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error'    => $files['error'][$i],
                    'size'     => $files['size'][$i],
                ];
                $up = handle_image_upload($single);
                if ($up['filename']) {
                    $stmt = db()->prepare("INSERT INTO gallery (image, caption, category) VALUES (?, ?, ?)");
                    $stmt->execute([$up['filename'], $captionDefault, $category]);
                    $count++;
                }
            }
        }
        $ok = "$count photo(s) uploaded.";
    }
}

// Edit single
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'edit' && $id) {
    if (!csrf_check($_POST['csrf'] ?? '')) {
        $err = 'Security token expired.';
    } else {
        $caption = trim($_POST['caption'] ?? '');
        $category = $_POST['category'] ?? 'general';
        if (!array_key_exists($category, $valid_categories)) $category = 'general';
        $sort = (int)($_POST['sort_order'] ?? 0);
        db()->prepare("UPDATE gallery SET caption = ?, category = ?, sort_order = ? WHERE id = ?")
            ->execute([$caption, $category, $sort, $id]);
        redirect(BASE_URL . '/admin/gallery.php?msg=saved');
    }
}

$record = null;
if ($action === 'edit' && $id) {
    $stmt = db()->prepare("SELECT * FROM gallery WHERE id = ?");
    $stmt->execute([$id]);
    $record = $stmt->fetch();
    if (!$record) redirect(BASE_URL . '/admin/gallery.php');
}

admin_header('gallery', 'Gallery');
?>

<?php if ($action === 'edit' && $record): ?>
  <div class="admin-bar">
    <h1 style="margin:0;">Edit Photo</h1>
    <a href="<?= BASE_URL ?>/admin/gallery.php" class="btn btn-ghost">← Back</a>
  </div>
  <form class="form" method="post" style="max-width:560px;margin:0;">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <img class="thumb" src="<?= UPLOAD_URL . '/' . e($record['image']) ?>" alt="" style="width:100%;aspect-ratio:auto;max-height:360px;object-fit:contain;background:#000;margin-bottom:1.5rem;">
    <div class="field">
      <label>Caption</label>
      <input type="text" name="caption" maxlength="255" value="<?= e($record['caption']) ?>">
    </div>
    <div class="field">
      <label>Category</label>
      <select name="category">
        <?php foreach ($valid_categories as $k => $label): ?>
          <option value="<?= e($k) ?>" <?= $record['category']===$k?'selected':'' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="field">
      <label>Sort Order (lower shows first)</label>
      <input type="number" name="sort_order" value="<?= (int)$record['sort_order'] ?>">
    </div>
    <button class="btn btn-primary" type="submit">Save</button>
  </form>

<?php else: ?>
  <div class="admin-bar"><h1 style="margin:0;">Gallery</h1></div>
  <?php if (($_GET['msg'] ?? '') === 'saved'):   ?><div class="alert success">Photo saved.</div><?php endif; ?>
  <?php if (($_GET['msg'] ?? '') === 'deleted'): ?><div class="alert success">Photo deleted.</div><?php endif; ?>
  <?php if ($ok):  ?><div class="alert success"><?= e($ok) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert error"><?= e($err) ?></div><?php endif; ?>

  <h2>Upload Photos</h2>
  <form class="form" method="post" enctype="multipart/form-data" action="?action=upload" style="max-width:620px;margin:0 0 3rem;">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <div class="field">
      <label>Photos (you can select multiple)</label>
      <input type="file" name="photos[]" accept="image/*" multiple required>
    </div>
    <div class="field">
      <label>Category</label>
      <select name="category">
        <?php foreach ($valid_categories as $k => $label): ?>
          <option value="<?= e($k) ?>"><?= e($label) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="field">
      <label>Caption (optional, applied to all)</label>
      <input type="text" name="caption" maxlength="255">
    </div>
    <button class="btn btn-primary" type="submit">Upload</button>
  </form>

  <h2>All Photos</h2>
  <?php $rows = db()->query("SELECT * FROM gallery ORDER BY category, sort_order ASC, created_at DESC")->fetchAll(); ?>
  <?php if (!$rows): ?>
    <p class="muted">No photos yet.</p>
  <?php else: ?>
    <div class="gallery-grid">
      <?php foreach ($rows as $g): ?>
        <div style="position:relative;">
          <div class="gallery-item"
               data-full="<?= UPLOAD_URL . '/' . e($g['image']) ?>"
               style="background-image:url('<?= UPLOAD_URL . '/' . e($g['image']) ?>');aspect-ratio:1/1;">
            <div class="caption">
              <?= e($valid_categories[$g['category']] ?? $g['category']) ?>
              <?php if ($g['caption']): ?> · <?= e($g['caption']) ?><?php endif; ?>
            </div>
          </div>
          <div style="margin-top:.4rem;display:flex;justify-content:space-between;font-size:.85rem;">
            <a href="?action=edit&id=<?= (int)$g['id'] ?>" class="action-link">Edit</a>
            <a href="?action=delete&id=<?= (int)$g['id'] ?>&csrf=<?= e(csrf_token()) ?>"
               class="action-link danger"
               onclick="return confirm('Delete this photo?');">Delete</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
<?php endif; ?>

<?php admin_footer(); ?>
