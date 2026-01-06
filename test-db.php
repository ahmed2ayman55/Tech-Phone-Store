<?php
/**
 * Database Connection Test
 * Run this file to verify your database connection is working
 * Usage: php test-db.php
 */

echo "Tech Phone Store - Database Connection Test\n";
echo "==========================================\n\n";

// Check if config file exists
if (!file_exists(__DIR__ . '/config/database.php')) {
    die("Error: config/database.php not found. Please configure your database first.\n");
}

require_once __DIR__ . '/config/database.php';

try {
    $db = getDB();
    echo "✓ Database connection successful!\n\n";
    
    // Test query
    $stmt = $db->query("SELECT DATABASE() as db_name, VERSION() as version");
    $info = $stmt->fetch();
    echo "Database: " . $info['db_name'] . "\n";
    echo "MySQL Version: " . $info['version'] . "\n\n";
    
    // Check tables
    echo "Checking tables...\n";
    $tables = ['users', 'products', 'orders', 'order_items', 'reviews', 'sessions'];
    $missing = [];
    
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "  ✓ Table '$table' exists\n";
        } else {
            echo "  ✗ Table '$table' is missing\n";
            $missing[] = $table;
        }
    }
    
    if (!empty($missing)) {
        echo "\n⚠ Warning: Missing tables: " . implode(', ', $missing) . "\n";
        echo "Please run database/schema.sql to create the tables.\n";
    } else {
        echo "\n✓ All required tables exist\n";
    }
    
    // Check for products
    $stmt = $db->query("SELECT COUNT(*) as count FROM products");
    $productCount = $stmt->fetch()['count'];
    
    if ($productCount > 0) {
        echo "✓ Found $productCount products in database\n";
    } else {
        echo "⚠ No products found. Seed data may not have been imported.\n";
    }
    
    echo "\n==========================================\n";
    echo "Database connection test completed!\n";
    echo "If you see any errors above, please fix them before proceeding.\n";
    
} catch (PDOException $e) {
    echo "✗ Database connection failed!\n\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    echo "Please check:\n";
    echo "1. MySQL server is running\n";
    echo "2. Database credentials in config/database.php are correct\n";
    echo "3. Database 'tech_phone_store' exists\n";
    echo "4. User has permissions to access the database\n";
    exit(1);
}
