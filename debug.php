<?php
// Debug script to identify deployment issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Deployment Debug Information</h1>";

// Check PHP version
echo "<h2>PHP Version</h2>";
echo "PHP Version: " . phpversion() . "<br>";

// Check required extensions
echo "<h2>Required Extensions</h2>";
$required = ['pdo', 'pdo_mysql', 'mbstring', 'json'];
foreach ($required as $ext) {
    $status = extension_loaded($ext) ? "✅ Available" : "❌ Missing";
    echo "$ext: $status<br>";
}

// Check config file
echo "<h2>Configuration File</h2>";
if (file_exists('includes/config.php')) {
    echo "✅ config.php exists<br>";
    try {
        require_once 'includes/config.php';
        echo "✅ config.php loaded successfully<br>";
        
        // Test database connection
        echo "<h2>Database Connection</h2>";
        try {
            require_once 'includes/db.php';
            $pdo = db();
            echo "✅ Database connection successful<br>";
            
            // Test if tables exist
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "Tables found: " . implode(', ', $tables) . "<br>";
            
        } catch (Exception $e) {
            echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ config.php error: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ config.php not found<br>";
    echo "Looking for config-production.php...<br>";
    if (file_exists('includes/config-production.php')) {
        echo "✅ config-production.php found - you need to rename it to config.php<br>";
    } else {
        echo "❌ config-production.php not found<br>";
    }
}

// Check file permissions
echo "<h2>File Permissions</h2>";
$paths = ['includes/', 'assets/', 'assets/uploads/'];
foreach ($paths as $path) {
    if (file_exists($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        echo "$path: $perms<br>";
    } else {
        echo "$path: ❌ Not found<br>";
    }
}

// Check .htaccess
echo "<h2>.htaccess File</h2>";
if (file_exists('.htaccess')) {
    echo "✅ .htaccess exists<br>";
} else {
    echo "❌ .htaccess not found<br>";
}

// Test basic functionality
echo "<h2>Basic Functionality Test</h2>";
try {
    echo "Current working directory: " . getcwd() . "<br>";
    echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
    echo "Server name: " . $_SERVER['SERVER_NAME'] . "<br>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "<h2>Next Steps</h2>";
echo "<ul>";
echo "<li>If config.php is missing, rename config-production.php to config.php</li>";
echo "<li>If database connection fails, check database credentials</li>";
echo "<li>If permissions are wrong, set directories to 755 and files to 644</li>";
echo "<li>Create assets/uploads/ directory if it doesn't exist</li>";
echo "</ul>";
?>
