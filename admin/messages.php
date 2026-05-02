<?php
require_once __DIR__ . '/_layout.php';
require_login();

$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($action === 'delete' && $id && csrf_check($_GET['csrf'] ?? '')) {
    db()->prepare("DELETE FROM messages WHERE id = ?")->execute([$id]);
    redirect(BASE_URL . '/admin/messages.php?msg=deleted');
}

$record = null;
if ($action === 'view' && $id) {
    $stmt = db()->prepare("SELECT * FROM messages WHERE id = ?");
    $stmt->execute([$id]);
    $record = $stmt->fetch();
    if ($record && !$record['is_read']) {
        db()->prepare("UPDATE messages SET is_read = 1 WHERE id = ?")->execute([$id]);
    }
}

admin_header('messages', 'Messages');
?>

<?php if ($action === 'view' && $record): ?>
  <div class="admin-bar">
    <h1 style="margin:0;">Message</h1>
    <a href="<?= BASE_URL ?>/admin/messages.php" class="btn btn-ghost">← Back</a>
  </div>
  <div class="card" style="padding:2rem;">
    <p class="muted" style="margin:0;font-size:.85rem;"><?= e(format_date($record['created_at'])) ?></p>
    <h2 style="margin:.5rem 0 .25rem;"><?= e($record['subject'] ?: '(no subject)') ?></h2>
    <p style="margin:0 0 1.5rem;">From <strong><?= e($record['name']) ?></strong> &lt;<a href="mailto:<?= e($record['email']) ?>"><?= e($record['email']) ?></a>&gt;</p>
    <div style="white-space:pre-line;border-top:1px solid var(--line);padding-top:1.5rem;"><?= e($record['body']) ?></div>
    <p style="margin-top:2rem;">
      <a class="btn btn-primary" href="mailto:<?= e($record['email']) ?>?subject=Re: <?= rawurlencode($record['subject'] ?: '') ?>">Reply by Email</a>
      <a class="action-link danger" href="?action=delete&id=<?= (int)$record['id'] ?>&csrf=<?= e(csrf_token()) ?>" onclick="return confirm('Delete this message?');">Delete</a>
    </p>
  </div>

<?php else: ?>
  <div class="admin-bar">
    <h1 style="margin:0;">Messages</h1>
  </div>
  <?php if (($_GET['msg'] ?? '') === 'deleted'): ?><div class="alert success">Message deleted.</div><?php endif; ?>

  <table class="data">
    <thead><tr><th>From</th><th>Subject</th><th>Received</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php
      $rows = db()->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();
      if (!$rows): ?>
        <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--muted);">No messages yet.</td></tr>
      <?php else: foreach ($rows as $r): ?>
        <tr style="<?= $r['is_read'] ? '' : 'font-weight:600;' ?>">
          <td><?= e($r['name']) ?><br><small class="muted"><?= e($r['email']) ?></small></td>
          <td><?= e($r['subject'] ?: '(no subject)') ?></td>
          <td><?= e(format_date($r['created_at'])) ?></td>
          <td><?= $r['is_read'] ? '<span class="muted">Read</span>' : '<span style="color:var(--gold);">New</span>' ?></td>
          <td>
            <a class="action-link" href="?action=view&id=<?= (int)$r['id'] ?>">View</a>
            <a class="action-link danger" href="?action=delete&id=<?= (int)$r['id'] ?>&csrf=<?= e(csrf_token()) ?>" onclick="return confirm('Delete?');">Delete</a>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
<?php endif; ?>

<?php admin_footer(); ?>
