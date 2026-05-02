<?php
// Simple test to check basic PHP functionality
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Basic PHP Test</h1>";
echo "PHP is working!<br>";
echo "Current directory: " . __DIR__ . "<br>";

// Check if config files exist
echo "<h2>Config Files</h2>";
if (file_exists('includes/config.php')) {
    echo "✅ config.php exists<br>";
} else {
    echo "❌ config.php missing<br>";
    if (file_exists('includes/config-production.php')) {
        echo "ℹ️ config-production.php exists - needs to be renamed<br>";
    }
}

// Test database connection without config
echo "<h2>Database Test</h2>";
try {
    $host = 'localhost';
    $dbname = 'u763598602_stephensn';
    $user = 'u763598602_stephensn';
    $pass = 'Stepehen_SN_2026';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    echo "✅ Database connection successful<br>";
    
    // Check if tables exist
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables: " . implode(', ', $tables) . "<br>";
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
}

echo "<h2>Next Steps</h2>";
echo "1. If config.php is missing, rename config-production.php to config.php<br>";
echo "2. If database connection fails, check database credentials<br>";
echo "3. If no tables exist, import complete-schema.sql<br>";
?>
