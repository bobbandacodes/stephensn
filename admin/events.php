<?php
require_once __DIR__ . '/_layout.php';
require_login();

$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$msg = '';
$err = '';

// Handle delete
if ($action === 'delete' && $id) {
    if (!csrf_check($_GET['csrf'] ?? '')) {
        $err = 'Invalid token.';
    } else {
        $stmt = db()->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$id]);
        redirect(BASE_URL . '/admin/events.php?msg=deleted');
    }
}

// Handle save (new or edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['new', 'edit'], true)) {
    if (!csrf_check($_POST['csrf'] ?? '')) {
        $err = 'Security token expired.';
    } else {
        $title       = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $event_date  = trim($_POST['event_date'] ?? '');
        $location    = trim($_POST['location'] ?? '');
        $register_url = trim($_POST['register_url'] ?? '');

        if ($title === '' || $event_date === '') {
            $err = 'Title and date are required.';
        } else {
            $image = $_POST['existing_image'] ?? null;
            if (!empty($_FILES['image']['name'])) {
                $up = handle_upload($_FILES['image']);
                if ($up['error']) { $err = $up['error']; }
                else { $image = $up['filename']; }
            }
            if (!$err) {
                $slug = slugify($title) . '-' . substr(md5($title . $event_date), 0, 6);
                if ($action === 'new') {
                    $stmt = db()->prepare("INSERT INTO events (title, slug, description, event_date, location, image, register_url) VALUES (?,?,?,?,?,?,?)");
                    $stmt->execute([$title, $slug, $description, $event_date, $location, $image, $register_url]);
                } else {
                    $stmt = db()->prepare("UPDATE events SET title=?, description=?, event_date=?, location=?, image=?, register_url=? WHERE id=?");
                    $stmt->execute([$title, $description, $event_date, $location, $image, $register_url, $id]);
                }
                redirect(BASE_URL . '/admin/events.php?msg=saved');
            }
        }
    }
}

function handle_upload(array $file): array {
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

// Load record for edit form
$record = null;
if ($action === 'edit' && $id) {
    $stmt = db()->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $record = $stmt->fetch();
    if (!$record) redirect(BASE_URL . '/admin/events.php');
}

admin_header('events', 'Events');
?>

<?php if ($action === 'list'): ?>
  <div class="admin-bar">
    <h1 style="margin:0;">Events</h1>
    <a href="?action=new" class="btn btn-primary">+ New Event</a>
  </div>
  <?php if (($_GET['msg'] ?? '') === 'saved'): ?><div class="alert success">Event saved.</div><?php endif; ?>
  <?php if (($_GET['msg'] ?? '') === 'deleted'): ?><div class="alert success">Event deleted.</div><?php endif; ?>

  <table class="data">
    <thead><tr><th>Title</th><th>Date</th><th>Location</th><th></th></tr></thead>
    <tbody>
      <?php
      $rows = db()->query("SELECT * FROM events ORDER BY event_date DESC")->fetchAll();
      if (!$rows): ?>
        <tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--muted);">No events yet.</td></tr>
      <?php else: foreach ($rows as $r): ?>
        <tr>
          <td><?= e($r['title']) ?></td>
          <td><?= e(format_date($r['event_date'])) ?></td>
          <td><?= e($r['location']) ?></td>
          <td>
            <a class="action-link" href="?action=edit&id=<?= (int)$r['id'] ?>">Edit</a>
            <a class="action-link danger" href="?action=delete&id=<?= (int)$r['id'] ?>&csrf=<?= e(csrf_token()) ?>" onclick="return confirm('Delete this event?');">Delete</a>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>

<?php else: ?>
  <div class="admin-bar">
    <h1 style="margin:0;"><?= $action === 'new' ? 'New Event' : 'Edit Event' ?></h1>
    <a href="<?= BASE_URL ?>/admin/events.php" class="btn btn-ghost">← Back</a>
  </div>

  <?php if ($err): ?><div class="alert error"><?= e($err) ?></div><?php endif; ?>

  <form class="form" method="post" enctype="multipart/form-data" style="max-width:760px;margin:0;">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="existing_image" value="<?= e($record['image'] ?? '') ?>">

    <div class="field">
      <label>Title</label>
      <input type="text" name="title" required maxlength="200" value="<?= e($record['title'] ?? ($_POST['title'] ?? '')) ?>">
    </div>
    <div class="field">
      <label>Date &amp; Time</label>
      <input type="datetime-local" name="event_date" required
             value="<?= e($record ? date('Y-m-d\TH:i', strtotime($record['event_date'])) : ($_POST['event_date'] ?? '')) ?>">
    </div>
    <div class="field">
      <label>Location</label>
      <input type="text" name="location" maxlength="255" value="<?= e($record['location'] ?? ($_POST['location'] ?? '')) ?>">
    </div>
    <div class="field">
      <label>Description</label>
      <textarea name="description"><?= e($record['description'] ?? ($_POST['description'] ?? '')) ?></textarea>
    </div>
    <div class="field">
      <label>Registration URL (optional)</label>
      <input type="url" name="register_url" maxlength="500" value="<?= e($record['register_url'] ?? ($_POST['register_url'] ?? '')) ?>">
    </div>
    <div class="field">
      <label>Cover Image (JPG/PNG/WebP, max 5MB)</label>
      <input type="file" name="image" accept="image/*">
      <?php if (!empty($record['image'])): ?>
        <p class="muted" style="margin-top:.5rem;font-size:.8rem;">Current: <?= e($record['image']) ?></p>
      <?php endif; ?>
    </div>
    <button class="btn btn-primary" type="submit">Save Event</button>
  </form>
<?php endif; ?>

<?php admin_footer(); ?>
