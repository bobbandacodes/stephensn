<?php
require_once __DIR__ . '/_layout.php';
require_login();

$err = ''; $ok = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) {
        $err = 'Security token expired.';
    } else {
        $current = (string)($_POST['current'] ?? '');
        $new     = (string)($_POST['new'] ?? '');
        $confirm = (string)($_POST['confirm'] ?? '');
        if (strlen($new) < 8)            $err = 'New password must be at least 8 characters.';
        elseif ($new !== $confirm)        $err = 'Passwords do not match.';
        else {
            $stmt = db()->prepare("SELECT password_hash FROM admins WHERE id = ?");
            $stmt->execute([$_SESSION['admin_id']]);
            $row = $stmt->fetch();
            if (!$row || !password_verify($current, $row['password_hash'])) {
                $err = 'Current password is incorrect.';
            } else {
                $hash = password_hash($new, PASSWORD_DEFAULT);
                db()->prepare("UPDATE admins SET password_hash = ? WHERE id = ?")
                    ->execute([$hash, $_SESSION['admin_id']]);
                $ok = 'Password updated.';
            }
        }
    }
}

admin_header('password', 'Change Password');
?>
<div class="admin-bar"><h1 style="margin:0;">Change Password</h1></div>
<?php if ($err): ?><div class="alert error"><?= e($err) ?></div><?php endif; ?>
<?php if ($ok): ?><div class="alert success"><?= e($ok) ?></div><?php endif; ?>
<form class="form" method="post" style="max-width:480px;margin:0;">
  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
  <div class="field"><label>Current Password</label><input type="password" name="current" required></div>
  <div class="field"><label>New Password</label><input type="password" name="new" required minlength="8"></div>
  <div class="field"><label>Confirm New Password</label><input type="password" name="confirm" required minlength="8"></div>
  <button class="btn btn-primary" type="submit">Update Password</button>
</form>
<?php admin_footer(); ?>
