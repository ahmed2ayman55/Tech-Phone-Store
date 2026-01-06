<?php
/**
 * List all admin accounts
 * Usage: php list-admins.php
 */

require_once __DIR__ . '/config/database.php';

$db = getDB();

$stmt = $db->prepare("SELECT id, email, first_name, last_name, created_at FROM users WHERE is_admin = 1 ORDER BY created_at DESC");
$stmt->execute();
$admins = $stmt->fetchAll();

echo "========================================\n";
echo "Admin Accounts List\n";
echo "========================================\n\n";

if (empty($admins)) {
    echo "No admin accounts found.\n";
    echo "Create one using: php create-admin.php email@example.com \"Password123\"\n\n";
} else {
    echo "Total Admins: " . count($admins) . "\n\n";
    
    foreach ($admins as $index => $admin) {
        echo ($index + 1) . ". " . $admin['email'] . "\n";
        echo "   Name: " . ($admin['first_name'] ?? 'N/A') . " " . ($admin['last_name'] ?? 'N/A') . "\n";
        echo "   Created: " . $admin['created_at'] . "\n";
        echo "   ID: " . $admin['id'] . "\n\n";
    }
}

echo "========================================\n";
