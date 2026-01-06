<?php
/**
 * Professional Admin Account Creator
 * Usage: php create-admin.php email@example.com "Password123"
 * 
 * This script creates admin accounts securely - no registration allowed
 */

require_once __DIR__ . '/config/database.php';

// Get command line arguments
$email = $argv[1] ?? null;
$password = $argv[2] ?? null;

if (!$email || !$password) {
    echo "========================================\n";
    echo "Admin Account Creator\n";
    echo "========================================\n\n";
    echo "Usage: php create-admin.php email@example.com \"Password123\"\n\n";
    echo "Example:\n";
    echo "  php create-admin.php admin@techstore.com \"SecurePass123!\"\n\n";
    echo "Requirements:\n";
    echo "  - Email must be valid format\n";
    echo "  - Password must be at least 8 characters\n";
    echo "  - Password should contain uppercase, lowercase, and numbers\n\n";
    exit(1);
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "❌ Error: Invalid email format\n";
    exit(1);
}

// Validate password strength
if (strlen($password) < 8) {
    echo "❌ Error: Password must be at least 8 characters\n";
    exit(1);
}

$db = getDB();

// Check if user already exists
$stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$existingUser = $stmt->fetch();

if ($existingUser) {
    // Update existing user to admin
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE users SET password_hash = ?, is_admin = 1 WHERE email = ?");
    $stmt->execute([$passwordHash, $email]);
    
    echo "✅ Existing user updated to admin\n";
    echo "   Email: $email\n";
    echo "   Password: Updated\n";
    echo "   Admin Status: Enabled\n\n";
} else {
    // Create new admin user
    $userId = 'admin_' . uniqid();
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("
        INSERT INTO users (id, email, first_name, last_name, password_hash, is_admin, created_at)
        VALUES (?, ?, 'Admin', 'User', ?, 1, NOW())
    ");
    $stmt->execute([$userId, $email, $passwordHash]);
    
    echo "✅ Admin account created successfully\n";
    echo "   User ID: $userId\n";
    echo "   Email: $email\n";
    echo "   Admin Status: Enabled\n\n";
}

echo "========================================\n";
echo "Next Steps:\n";
echo "1. Go to: http://localhost:8000/admin-login.php\n";
echo "2. Login with the credentials above\n";
echo "3. Access admin dashboard\n";
echo "========================================\n";
