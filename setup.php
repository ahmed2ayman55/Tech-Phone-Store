<?php
/**
 * Setup script for Tech Phone Store
 * Run this file once to initialize the database
 */

echo "Tech Phone Store - Setup Script\n";
echo "==============================\n\n";

// Check PHP version
if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    die("Error: PHP 7.4 or higher is required. You have " . PHP_VERSION . "\n");
}

echo "✓ PHP version: " . PHP_VERSION . "\n";

// Check if PDO MySQL is available
if (!extension_loaded('pdo_mysql')) {
    die("Error: PDO MySQL extension is not loaded. Please install php-mysql extension.\n");
}

echo "✓ PDO MySQL extension loaded\n\n";

// Get database credentials
echo "Please enter your MySQL database credentials:\n";
echo "Press Enter to use defaults (shown in brackets)\n\n";

$host = readline("Database Host [localhost]: ") ?: 'localhost';
$name = readline("Database Name [tech_phone_store]: ") ?: 'tech_phone_store';
$user = readline("Database User [root]: ") ?: 'root';
$pass = readline("Database Password []: ") ?: '';

echo "\n";

// Test connection
try {
    $dsn = "mysql:host=$host;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "✓ Connected to MySQL server\n";
} catch (PDOException $e) {
    die("Error: Could not connect to MySQL server: " . $e->getMessage() . "\n");
}

// Create database if it doesn't exist
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database '$name' created or already exists\n";
} catch (PDOException $e) {
    die("Error: Could not create database: " . $e->getMessage() . "\n");
}

// Connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$name;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "✓ Connected to database '$name'\n";
} catch (PDOException $e) {
    die("Error: Could not connect to database: " . $e->getMessage() . "\n");
}

// Read and execute schema file
$schemaFile = __DIR__ . '/database/schema.sql';
if (!file_exists($schemaFile)) {
    die("Error: Schema file not found: $schemaFile\n");
}

echo "\nReading schema file...\n";
$schema = file_get_contents($schemaFile);

// Remove USE statement if present (we're already using the database)
$schema = preg_replace('/USE\s+[^;]+;/i', '', $schema);

// Split into individual statements
$statements = array_filter(
    array_map('trim', explode(';', $schema)),
    function($stmt) {
        return !empty($stmt) && !preg_match('/^--/', $stmt);
    }
);

echo "Executing " . count($statements) . " SQL statements...\n";

$pdo->beginTransaction();
try {
    foreach ($statements as $statement) {
        if (!empty(trim($statement))) {
            $pdo->exec($statement);
        }
    }
    $pdo->commit();
    echo "✓ Database schema imported successfully\n";
} catch (PDOException $e) {
    $pdo->rollBack();
    die("Error: Failed to import schema: " . $e->getMessage() . "\n");
}

// Update config file
$configFile = __DIR__ . '/config/database.php';
$configContent = <<<PHP
<?php
// Database configuration
define('DB_HOST', '$host');
define('DB_NAME', '$name');
define('DB_USER', '$user');
define('DB_PASS', '$pass');
define('DB_CHARSET', 'utf8mb4');

// Create database connection
function getDB() {
    static \$pdo = null;
    
    if (\$pdo === null) {
        try {
            \$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            \$options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            \$pdo = new PDO(\$dsn, DB_USER, DB_PASS, \$options);
        } catch (PDOException \$e) {
            error_log("Database connection failed: " . \$e->getMessage());
            die("Database connection failed. Please check your configuration.");
        }
    }
    
    return \$pdo;
}
PHP;

file_put_contents($configFile, $configContent);
echo "✓ Configuration file updated: $configFile\n";

// Verify tables were created
$tables = ['users', 'products', 'orders', 'order_items', 'reviews', 'sessions'];
$missing = [];

foreach ($tables as $table) {
    $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
    if ($stmt->rowCount() === 0) {
        $missing[] = $table;
    }
}

if (!empty($missing)) {
    echo "\n⚠ Warning: Some tables may not have been created: " . implode(', ', $missing) . "\n";
} else {
    echo "✓ All required tables exist\n";
}

// Check for seed data
$stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
$productCount = $stmt->fetch()['count'];

if ($productCount > 0) {
    echo "✓ Seed data found ($productCount products)\n";
} else {
    echo "⚠ No products found. Seed data may not have been imported.\n";
}

echo "\n";
echo "==============================\n";
echo "Setup completed successfully!\n";
echo "==============================\n\n";
echo "Next steps:\n";
echo "1. Start PHP server: php -S localhost:8000\n";
echo "2. Open browser: http://localhost:8000\n";
echo "3. Register an account (use email with 'admin' for admin access)\n";
echo "\n";
