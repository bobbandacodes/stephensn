<?php
require_once __DIR__ . '/_layout.php';
require_once __DIR__ . '/../includes/upload.php';
require_login();

$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$err = '';

if ($action === 'delete' && $id && csrf_check($_GET['csrf'] ?? '')) {
    $stmt = db()->prepare("SELECT featured_image FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row) delete_upload($row['featured_image']);
    db()->prepare("DELETE FROM posts WHERE id = ?")->execute([$id]);
    redirect(BASE_URL . '/admin/posts.php?msg=deleted');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['new','edit'], true)) {
    if (!csrf_check($_POST['csrf'] ?? '')) {
        $err = 'Security token expired.';
    } else {
        $title   = trim($_POST['title'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $body    = trim($_POST['body'] ?? '');
        $author  = trim($_POST['author'] ?? '') ?: setting('site_name', 'Prophet Stephen SN');
        $publish = !empty($_POST['is_published']) ? 1 : 0;
        $publishedAt = trim($_POST['published_at'] ?? '');

        if ($title === '' || $body === '') {
            $err = 'Title and body are required.';
        } else {
            $existing = $_POST['existing_image'] ?? null;
            $featured = $existing;
            if (!empty($_FILES['featured_image']['name'])) {
                $up = handle_image_upload($_FILES['featured_image']);
                if ($up['error']) { $err = $up['error']; }
                elseif ($up['filename']) {
                    delete_upload($existing);
                    $featured = $up['filename'];
                }
            }
            if (!empty($_POST['remove_image'])) {
                delete_upload($featured);
                $featured = null;
            }
            if (!$err) {
                $slug = slugify($title) . '-' . substr(md5($title . microtime()), 0, 6);
                $publishedAtSql = $publishedAt ? date('Y-m-d H:i:s', strtotime($publishedAt)) : date('Y-m-d H:i:s');

                if ($action === 'new') {
                    $stmt = db()->prepare("INSERT INTO posts (title, slug, excerpt, body, featured_image, author, is_published, published_at) VALUES (?,?,?,?,?,?,?,?)");
                    $stmt->execute([$title, $slug, $excerpt, $body, $featured, $author, $publish, $publishedAtSql]);
                } else {
                    $stmt = db()->prepare("UPDATE posts SET title=?, excerpt=?, body=?, featured_image=?, author=?, is_published=?, published_at=? WHERE id=?");
                    $stmt->execute([$title, $excerpt, $body, $featured, $author, $publish, $publishedAtSql, $id]);
                }
                redirect(BASE_URL . '/admin/posts.php?msg=saved');
            }
        }
    }
}

$record = null;
if ($action === 'edit' && $id) {
    $stmt = db()->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    $record = $stmt->fetch();
    if (!$record) redirect(BASE_URL . '/admin/posts.php');
}

admin_header('posts', 'Blog Posts');
?>

<?php if ($action === 'list'): ?>
  <div class="admin-bar">
    <h1 style="margin:0;">Blog Posts</h1>
    <a href="?action=new" class="btn btn-primary">+ New Post</a>
  </div>
  <?php if (($_GET['msg'] ?? '') === 'saved'): ?><div class="alert success">Post saved.</div><?php endif; ?>
  <?php if (($_GET['msg'] ?? '') === 'deleted'): ?><div class="alert success">Post deleted.</div><?php endif; ?>

  <table class="data">
    <thead><tr><th>Title</th><th>Published</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php
      $rows = db()->query("SELECT * FROM posts ORDER BY published_at DESC")->fetchAll();
      if (!$rows): ?>
        <tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--muted);">No posts yet. <a href="?action=new">Write the first one</a>.</td></tr>
      <?php else: foreach ($rows as $r): ?>
        <tr>
          <td><strong><?= e($r['title']) ?></strong><?php if ($r['excerpt']): ?><br><small class="muted"><?= e(mb_strimwidth($r['excerpt'], 0, 80, '…')) ?></small><?php endif; ?></td>
          <td><?= e(format_date($r['published_at'], 'M j, Y')) ?></td>
          <td><?= $r['is_published'] ? '<span style="color:var(--accent);">Published</span>' : '<span class="muted">Draft</span>' ?></td>
          <td>
            <a class="action-link" href="?action=edit&id=<?= (int)$r['id'] ?>">Edit</a>
            <?php if ($r['is_published']): ?>
              <a class="action-link" href="<?= BASE_URL ?>/post.php?slug=<?= urlencode($r['slug']) ?>" target="_blank">View</a>
            <?php endif; ?>
            <a class="action-link danger" href="?action=delete&id=<?= (int)$r['id'] ?>&csrf=<?= e(csrf_token()) ?>" onclick="return confirm('Delete this post?');">Delete</a>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>

<?php else: ?>
  <div class="admin-bar">
    <h1 style="margin:0;"><?= $action === 'new' ? 'New Post' : 'Edit Post' ?></h1>
    <a href="<?= BASE_URL ?>/admin/posts.php" class="btn btn-ghost">← Back</a>
  </div>

  <?php if ($err): ?><div class="alert error"><?= e($err) ?></div><?php endif; ?>

  <form class="form" method="post" enctype="multipart/form-data" style="max-width:820px;margin:0;">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="existing_image" value="<?= e($record['featured_image'] ?? '') ?>">

    <div class="field">
      <label>Title</label>
      <input type="text" name="title" required maxlength="220" value="<?= e($record['title'] ?? '') ?>">
    </div>
    <div class="field">
      <label>Excerpt (short summary, optional)</label>
      <textarea name="excerpt" rows="2" maxlength="500"><?= e($record['excerpt'] ?? '') ?></textarea>
    </div>
    <div class="field">
      <label>Body</label>
      <textarea name="body" rows="14" required><?= e($record['body'] ?? '') ?></textarea>
    </div>
    <div class="field">
      <label>Author</label>
      <input type="text" name="author" maxlength="120" value="<?= e($record['author'] ?? setting('site_name', '')) ?>">
    </div>
    <div class="field">
      <label>Publish Date</label>
      <input type="datetime-local" name="published_at"
             value="<?= e($record ? date('Y-m-d\TH:i', strtotime($record['published_at'])) : date('Y-m-d\TH:i')) ?>">
    </div>
    <div class="image-slot">
      <label>Featured Image</label>
      <div style="display:flex;gap:1.25rem;align-items:flex-start;flex-wrap:wrap;">
        <?php if (!empty($record['featured_image'])): ?>
          <img class="thumb" src="<?= UPLOAD_URL . '/' . e($record['featured_image']) ?>" alt="">
        <?php else: ?>
          <div class="thumb" style="display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:.8rem;">No image</div>
        <?php endif; ?>
        <div style="flex:1;min-width:240px;">
          <input type="file" name="featured_image" accept="image/*">
          <?php if (!empty($record['featured_image'])): ?>
            <p style="margin:.6rem 0 0;font-size:.85rem;"><label><input type="checkbox" name="remove_image"> Remove current image</label></p>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="field">
      <label><input type="checkbox" name="is_published" value="1" <?= (!$record || $record['is_published']) ? 'checked' : '' ?>> Published (uncheck to save as draft)</label>
    </div>
    <button class="btn btn-primary" type="submit">Save Post</button>
  </form>
<?php endif; ?>

<?php admin_footer(); ?>
