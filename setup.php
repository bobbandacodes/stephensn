<?php
// One-time setup: creates the default admin user and seeds settings.
// DELETE THIS FILE after running it once.
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/settings.php';

$username = 'admin';
$password = 'temporary';

try {
    seed_default_settings();

    $stmt = db()->prepare("SELECT id FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo "Settings seeded. Admin '$username' already exists.<br>";
        echo "<strong style='color:#c00;'>DELETE setup.php now.</strong>";
        exit;
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = db()->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
    $stmt->execute([$username, $hash]);
    echo "Settings seeded.<br>Admin created.<br>Username: <strong>$username</strong><br>Password: <strong>$password</strong><br><br>";
    echo "<strong style='color:#c00;'>DELETE setup.php now.</strong> Then log in at <a href='" . BASE_URL . "/admin/login.php'>" . BASE_URL . "/admin/login.php</a> and change your password.";
} catch (Throwable $ex) {
    echo "Setup error: " . htmlspecialchars($ex->getMessage());
}
