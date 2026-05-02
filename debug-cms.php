<?php
// CMS Debug Script - Check all content types
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/db.php';

echo "<h1>CMS Content Debug</h1>";

// Check database connection
echo "<h2>Database Connection</h2>";
try {
    $pdo = db();
    echo "✅ Database connected<br>";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
    exit;
}

// Check tables exist
echo "<h2>Database Tables</h2>";
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "Tables found: " . implode(', ', $tables) . "<br>";

// Check Events
echo "<h2>Events</h2>";
$events = $pdo->query("SELECT COUNT(*) as count FROM events")->fetch();
echo "Total events: " . $events['count'] . "<br>";

$upcoming = $pdo->query("SELECT COUNT(*) as count FROM events WHERE event_date >= NOW()")->fetch();
echo "Upcoming events: " . $upcoming['count'] . "<br>";

if ($events['count'] > 0) {
    $latest = $pdo->query("SELECT * FROM events ORDER BY created_at DESC LIMIT 3")->fetchAll();
    echo "<h3>Latest Events:</h3>";
    foreach ($latest as $event) {
        echo "- " . htmlspecialchars($event['title']) . " (" . $event['event_date'] . ")<br>";
    }
}

// Check Posts
echo "<h2>Posts</h2>";
$posts = $pdo->query("SELECT COUNT(*) as count FROM posts")->fetch();
echo "Total posts: " . $posts['count'] . "<br>";

$published = $pdo->query("SELECT COUNT(*) as count FROM posts WHERE is_published = 1")->fetch();
echo "Published posts: " . $published['count'] . "<br>";

if ($posts['count'] > 0) {
    $latest = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC LIMIT 3")->fetchAll();
    echo "<h3>Latest Posts:</h3>";
    foreach ($latest as $post) {
        echo "- " . htmlspecialchars($post['title']) . " (Published: " . ($post['is_published'] ? 'Yes' : 'No') . ")<br>";
    }
}

// Check Gallery
echo "<h2>Gallery</h2>";
$gallery = $pdo->query("SELECT COUNT(*) as count FROM gallery")->fetch();
echo "Total gallery items: " . $gallery['count'] . "<br>";

if ($gallery['count'] > 0) {
    $latest = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC LIMIT 3")->fetchAll();
    echo "<h3>Latest Gallery Items:</h3>";
    foreach ($latest as $item) {
        echo "- " . htmlspecialchars($item['caption'] ?: 'No caption') . " (" . $item['category'] . ")<br>";
    }
}

// Check Sermons
echo "<h2>Sermons</h2>";
$sermons = $pdo->query("SELECT COUNT(*) as count FROM sermons")->fetch();
echo "Total sermons: " . $sermons['count'] . "<br>";

if ($sermons['count'] > 0) {
    $latest = $pdo->query("SELECT * FROM sermons ORDER BY created_at DESC LIMIT 3")->fetchAll();
    echo "<h3>Latest Sermons:</h3>";
    foreach ($latest as $sermon) {
        echo "- " . htmlspecialchars($sermon['title']) . " (" . ($sermon['preached_on'] ?: 'No date') . ")<br>";
    }
}

echo "<h2>Troubleshooting</h2>";
echo "<ul>";
echo "<li>If counts are 0, you need to add content via admin panel</li>";
echo "<li>If posts exist but don't show, check is_published status</li>";
echo "<li>If events exist but don't show, check event_date (future/past)</li>";
echo "<li>If gallery items exist but don't show, check image paths</li>";
echo "</ul>";

echo "<h2>Quick Links</h2>";
echo "<a href='/admin/'>Admin Panel</a><br>";
echo "<a href='/events.php'>Events Page</a><br>";
echo "<a href='/blog.php'>Blog Page</a><br>";
echo "<a href='/gallery.php'>Gallery Page</a><br>";
echo "<a href='/media.php'>Media Page</a><br>";
?>
