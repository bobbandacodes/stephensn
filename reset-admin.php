<?php
// One-time admin password reset. DELETE this file after use.
require_once __DIR__ . '/includes/db.php';

$username = 'admin';
$password = 'temporary';

try {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = db()->prepare("SELECT id FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $row = $stmt->fetch();

    if ($row) {
        db()->prepare("UPDATE admins SET password_hash = ? WHERE id = ?")
            ->execute([$hash, $row['id']]);
        echo "Password reset for '$username'.<br>";
    } else {
        db()->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)")
            ->execute([$username, $hash]);
        echo "Admin '$username' created.<br>";
    }

    echo "Username: <strong>$username</strong><br>";
    echo "Password: <strong>$password</strong><br><br>";
    echo "Verify roundtrip: " . (password_verify($password, $hash) ? "OK ✓" : "FAILED ✗") . "<br><br>";
    echo "<strong style='color:#c00;'>DELETE reset-admin.php now.</strong> Then log in at <a href='" . BASE_URL . "/admin/login.php'>" . BASE_URL . "/admin/login.php</a>";
} catch (Throwable $ex) {
    echo "Error: " . htmlspecialchars($ex->getMessage());
}
