<?php
require_once __DIR__ . '/_layout.php';
require_login();

$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$err = '';

function sermon_upload(array $file): array {
    if ($file['error'] === UPLOAD_ERR_NO_FILE) return ['filename' => null, 'error' => null];
    if ($file['error'] !== UPLOAD_ERR_OK) return ['filename' => null, 'error' => 'Upload failed.'];
    if ($file['size'] > 5 * 1024 * 1024) return ['filename' => null, 'error' => 'Image too large (max 5MB).'];
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!isset($allowed[$mime])) return ['filename' => null, 'error' => 'Only JPG, PNG, or WebP allowed.'];
    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
    $name = bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . '/' . $name)) {
        return ['filename' => null, 'error' => 'Could not save upload.'];
    }
    return ['filename' => $name, 'error' => null];
}

if ($action === 'delete' && $id) {
    if (csrf_check($_GET['csrf'] ?? '')) {
        db()->prepare("DELETE FROM sermons WHERE id = ?")->execute([$id]);
        redirect(BASE_URL . '/admin/sermons.php?msg=deleted');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['new','edit'], true)) {
    if (!csrf_check($_POST['csrf'] ?? '')) {
        $err = 'Security token expired.';
    } else {
        $title       = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $video_url   = trim($_POST['video_url'] ?? '');
        $preached_on = trim($_POST['preached_on'] ?? '') ?: null;

        if ($title === '') { $err = 'Title is required.'; }
        else {
            $thumb = $_POST['existing_thumb'] ?? null;
            if (!empty($_FILES['thumbnail']['name'])) {
                $up = sermon_upload($_FILES['thumbnail']);
                if ($up['error']) $err = $up['error'];
                else $thumb = $up['filename'];
            }
            if (!$err) {
                if ($action === 'new') {
                    $stmt = db()->prepare("INSERT INTO sermons (title, description, video_url, thumbnail, preached_on) VALUES (?,?,?,?,?)");
                    $stmt->execute([$title, $description, $video_url, $thumb, $preached_on]);
                } else {
                    $stmt = db()->prepare("UPDATE sermons SET title=?, description=?, video_url=?, thumbnail=?, preached_on=? WHERE id=?");
                    $stmt->execute([$title, $description, $video_url, $thumb, $preached_on, $id]);
                }
                redirect(BASE_URL . '/admin/sermons.php?msg=saved');
            }
        }
    }
}

$record = null;
if ($action === 'edit' && $id) {
    $stmt = db()->prepare("SELECT * FROM sermons WHERE id = ?");
    $stmt->execute([$id]);
    $record = $stmt->fetch();
    if (!$record) redirect(BASE_URL . '/admin/sermons.php');
}

admin_header('sermons', 'Sermons');
?>

<?php if ($action === 'list'): ?>
  <div class="admin-bar">
    <h1 style="margin:0;">Sermons</h1>
    <a href="?action=new" class="btn btn-primary">+ New Sermon</a>
  </div>
  <?php if (($_GET['msg'] ?? '') === 'saved'): ?><div class="alert success">Sermon saved.</div><?php endif; ?>
  <?php if (($_GET['msg'] ?? '') === 'deleted'): ?><div class="alert success">Sermon deleted.</div><?php endif; ?>

  <table class="data">
    <thead><tr><th>Title</th><th>Preached On</th><th>Video</th><th></th></tr></thead>
    <tbody>
      <?php
      $rows = db()->query("SELECT * FROM sermons ORDER BY COALESCE(preached_on, created_at) DESC")->fetchAll();
      if (!$rows): ?>
        <tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--muted);">No sermons yet.</td></tr>
      <?php else: foreach ($rows as $r): ?>
        <tr>
          <td><?= e($r['title']) ?></td>
          <td><?= e(format_date($r['preached_on'] ?? $r['created_at'], 'M j, Y')) ?></td>
          <td><?= $r['video_url'] ? '<a href="' . e($r['video_url']) . '" target="_blank">link</a>' : '—' ?></td>
          <td>
            <a class="action-link" href="?action=edit&id=<?= (int)$r['id'] ?>">Edit</a>
            <a class="action-link danger" href="?action=delete&id=<?= (int)$r['id'] ?>&csrf=<?= e(csrf_token()) ?>" onclick="return confirm('Delete this sermon?');">Delete</a>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>

<?php else: ?>
  <div class="admin-bar">
    <h1 style="margin:0;"><?= $action === 'new' ? 'New Sermon' : 'Edit Sermon' ?></h1>
    <a href="<?= BASE_URL ?>/admin/sermons.php" class="btn btn-ghost">← Back</a>
  </div>

  <?php if ($err): ?><div class="alert error"><?= e($err) ?></div><?php endif; ?>

  <form class="form" method="post" enctype="multipart/form-data" style="max-width:760px;margin:0;">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="existing_thumb" value="<?= e($record['thumbnail'] ?? '') ?>">

    <div class="field">
      <label>Title</label>
      <input type="text" name="title" required maxlength="200" value="<?= e($record['title'] ?? '') ?>">
    </div>
    <div class="field">
      <label>Preached On</label>
      <input type="date" name="preached_on" value="<?= e($record['preached_on'] ?? '') ?>">
    </div>
    <div class="field">
      <label>Video URL (YouTube, Vimeo, etc.)</label>
      <input type="url" name="video_url" maxlength="500" value="<?= e($record['video_url'] ?? '') ?>">
    </div>
    <div class="field">
      <label>Description</label>
      <textarea name="description"><?= e($record['description'] ?? '') ?></textarea>
    </div>
    <div class="field">
      <label>Thumbnail</label>
      <input type="file" name="thumbnail" accept="image/*">
      <?php if (!empty($record['thumbnail'])): ?>
        <p class="muted" style="margin-top:.5rem;font-size:.8rem;">Current: <?= e($record['thumbnail']) ?></p>
      <?php endif; ?>
    </div>
    <button class="btn btn-primary" type="submit">Save Sermon</button>
  </form>
<?php endif; ?>

<?php admin_footer(); ?>
