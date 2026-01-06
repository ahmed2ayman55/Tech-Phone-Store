<?php
/**
 * Quick script to make a user admin
 * Usage: php fix-admin.php admin@test.com
 */

require_once __DIR__ . '/config/database.php';

$email = $argv[1] ?? null;

if (!$email) {
    echo "Usage: php fix-admin.php email@example.com\n";
    echo "Example: php fix-admin.php admin@test.com\n";
    exit(1);
}

$db = getDB();

// Find user
$stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    echo "Error: User with email '$email' not found.\n";
    echo "Please register first at http://localhost:8000/login.php\n";
    exit(1);
}

// Update to admin
$stmt = $db->prepare("UPDATE users SET is_admin = 1 WHERE email = ?");
$stmt->execute([$email]);

echo "✅ User '$email' is now an admin!\n";
echo "Please login again to refresh your session.\n";
echo "Then go to: http://localhost:8000/admin.php\n";
