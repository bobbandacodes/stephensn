<?php
require_once __DIR__ . '/../includes/auth.php';

if (is_logged_in()) redirect(BASE_URL . '/admin/dashboard.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) {
        $error = 'Security token expired. Try again.';
    } else {
        $u = trim($_POST['username'] ?? '');
        $p = (string)($_POST['password'] ?? '');
        if (login_admin($u, $p)) {
            redirect(BASE_URL . '/admin/dashboard.php');
        }
        $error = 'Invalid credentials.';
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — <?= e(SITE_NAME) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body style="display:flex;min-height:100vh;align-items:center;justify-content:center;padding:2rem;">
  <div style="width:100%;max-width:420px;">
    <div style="text-align:center;margin-bottom:2rem;">
      <h1 style="font-size:2rem;margin:0;color:var(--gold);"><?= e(SITE_NAME) ?></h1>
      <p class="muted" style="letter-spacing:.25em;text-transform:uppercase;font-size:.7rem;">Admin Console</p>
    </div>
    <form class="form" method="post">
      <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <div class="field">
        <label>Username</label>
        <input type="text" name="username" required autofocus>
      </div>
      <div class="field">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <button class="btn btn-primary" style="width:100%;" type="submit">Sign In</button>
    </form>
  </div>
</body>
</html>
