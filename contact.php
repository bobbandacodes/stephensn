<?php
require_once __DIR__ . '/includes/db.php';
$page = 'contact';
$pageTitle = 'Contact';

$errors = [];
$success = false;
$old = ['name' => '', 'email' => '', 'subject' => '', 'body' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) {
        $errors[] = 'Security token expired. Please try again.';
    } else {
        $old['name']    = trim($_POST['name'] ?? '');
        $old['email']   = trim($_POST['email'] ?? '');
        $old['subject'] = trim($_POST['subject'] ?? '');
        $old['body']    = trim($_POST['body'] ?? '');

        if ($old['name'] === '' || mb_strlen($old['name']) > 120) $errors[] = 'Please enter your name.';
        if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email.';
        if ($old['body'] === '' || mb_strlen($old['body']) > 5000) $errors[] = 'Please enter a message (under 5000 chars).';

        if (!$errors) {
            $stmt = db()->prepare("INSERT INTO messages (name, email, subject, body) VALUES (?, ?, ?, ?)");
            $stmt->execute([$old['name'], $old['email'], $old['subject'], $old['body']]);
            $success = true;
            $old = ['name' => '', 'email' => '', 'subject' => '', 'body' => ''];
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="wrap">
    <span class="eyebrow">Get in Touch</span>
    <h1>Contact</h1>
    <p class="muted">For invitations, partnership, prayer requests, or general inquiries.</p>
  </div>
</section>

<section class="block">
  <div class="wrap">
    <form class="form" method="post" action="">
      <?php if ($success): ?>
        <div class="alert success">Thank you. Your message has been received — we'll respond shortly.</div>
      <?php endif; ?>
      <?php foreach ($errors as $err): ?>
        <div class="alert error"><?= e($err) ?></div>
      <?php endforeach; ?>

      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

      <div class="field">
        <label>Your name</label>
        <input type="text" name="name" required maxlength="120" value="<?= e($old['name']) ?>">
      </div>
      <div class="field">
        <label>Email</label>
        <input type="email" name="email" required maxlength="160" value="<?= e($old['email']) ?>">
      </div>
      <div class="field">
        <label>Subject</label>
        <input type="text" name="subject" maxlength="200" value="<?= e($old['subject']) ?>">
      </div>
      <div class="field">
        <label>Message</label>
        <textarea name="body" required maxlength="5000"><?= e($old['body']) ?></textarea>
      </div>
      <button class="btn btn-primary" type="submit">Send Message</button>
    </form>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
