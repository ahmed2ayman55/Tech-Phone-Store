<?php
/**
 * Seed Admin Account - Creates default admin for testing
 * Run: php seed-admin.php
 */

require_once __DIR__ . '/config/database.php';

$db = getDB();

// Default admin credentials
$adminEmail = 'admin@techstore.com';
$adminPassword = 'Admin123!';
$adminFirstName = 'Admin';
$adminLastName = 'User';

echo "========================================\n";
echo "Creating Default Admin Account\n";
echo "========================================\n\n";

// Check if admin already exists
$stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$adminEmail]);
$existingAdmin = $stmt->fetch();

if ($existingAdmin) {
    // Update existing admin password
    $passwordHash = password_hash($adminPassword, PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE users SET password_hash = ?, is_admin = 1, first_name = ?, last_name = ? WHERE email = ?");
    $stmt->execute([$passwordHash, $adminFirstName, $adminLastName, $adminEmail]);
    
    echo "✅ Admin account updated!\n\n";
} else {
    // Create new admin
    $adminId = 'admin_' . uniqid();
    $passwordHash = password_hash($adminPassword, PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("
        INSERT INTO users (id, email, first_name, last_name, password_hash, is_admin, created_at)
        VALUES (?, ?, ?, ?, ?, 1, NOW())
    ");
    $stmt->execute([$adminId, $adminEmail, $adminFirstName, $adminLastName, $passwordHash]);
    
    echo "✅ Admin account created successfully!\n\n";
}

echo "========================================\n";
echo "Admin Credentials:\n";
echo "========================================\n";
echo "Email:    $adminEmail\n";
echo "Password: $adminPassword\n";
echo "========================================\n\n";

echo "Next Steps:\n";
echo "1. Go to: http://localhost:8000/admin-login.php\n";
echo "2. Login with the credentials above\n";
echo "3. Access admin dashboard\n";
echo "========================================\n";
