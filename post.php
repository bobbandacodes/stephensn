<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/settings.php';
$page = 'blog';

$slug = $_GET['slug'] ?? '';
$stmt = db()->prepare("SELECT * FROM posts WHERE slug = ? AND is_published = 1 LIMIT 1");
$stmt->execute([$slug]);
$post = $stmt->fetch();

if (!$post) {
    http_response_code(404);
    $pageTitle = 'Post not found';
    include __DIR__ . '/includes/header.php';
    echo '<section class="block"><div class="wrap"><h1>Post not found</h1><p class="muted">This post may have been moved or unpublished.</p><p><a href="' . BASE_URL . '/blog.php" class="btn btn-ghost">Back to blog</a></p></div></section>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $post['title'];
$cover = $post['featured_image'] ? UPLOAD_URL . '/' . rawurlencode($post['featured_image']) : null;

include __DIR__ . '/includes/header.php';
?>

<section class="page-hero <?= $cover ? 'has-image' : '' ?>"
         <?= $cover ? 'style="background-image:url(\'' . e($cover) . '\');"' : '' ?>>
  <div class="wrap">
    <span class="eyebrow"><?= e(format_date($post['published_at'], 'M j, Y')) ?> · <?= e($post['author']) ?></span>
    <h1><?= e($post['title']) ?></h1>
  </div>
</section>

<section class="block">
  <div class="wrap" style="max-width:760px;">
    <?php if ($post['excerpt']): ?>
      <p style="font-size:1.2rem;color:var(--accent-3);font-style:italic;margin-bottom:2rem;"><?= e($post['excerpt']) ?></p>
    <?php endif; ?>
    <div class="post-body">
      <?= nl2br(e($post['body'])) ?>
    </div>
    <p style="margin-top:3rem;"><a href="<?= BASE_URL ?>/blog.php" class="btn btn-ghost">← All Posts</a></p>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
