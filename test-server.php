<?php
/**
 * Simple test page to verify PHP is working
 * Access this file directly in browser: http://localhost:8000/test-server.php
 */

echo "<!DOCTYPE html>";
echo "<html><head><title>PHP Test</title></head><body>";
echo "<h1>✅ PHP is Working!</h1>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";
echo "<p>Server Time: " . date('Y-m-d H:i:s') . "</p>";

// Test database connection
if (file_exists(__DIR__ . '/config/database.php')) {
    echo "<h2>Database Connection Test</h2>";
    try {
        require_once __DIR__ . '/config/database.php';
        $db = getDB();
        echo "<p style='color: green;'>✅ Database connection successful!</p>";
        
        // Check tables
        $stmt = $db->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<p>Tables found: " . count($tables) . "</p>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
        
        // Check products
        $stmt = $db->query("SELECT COUNT(*) as count FROM products");
        $count = $stmt->fetch()['count'];
        echo "<p>Products in database: $count</p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ config/database.php not found</p>";
}

echo "<hr>";
echo "<p><a href='/'>Go to Homepage</a></p>";
echo "<p><a href='/products.php'>Go to Products</a></p>";
echo "</body></html>";
