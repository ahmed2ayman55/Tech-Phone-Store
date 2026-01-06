<?php
// Debug session to see what's stored
session_start();

echo "<h1>Session Debug Information</h1>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n\n";
echo "Session Data:\n";
print_r($_SESSION);
echo "\n\n";

// Check authentication
echo "isAuthenticated(): " . (isset($_SESSION['user_id']) ? 'YES' : 'NO') . "\n";
echo "isAdmin(): " . (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true ? 'YES' : 'NO') . "\n";
echo "is_admin value: ";
var_dump($_SESSION['is_admin'] ?? 'NOT SET');
echo "\n\n";

// Check database
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/config/database.php';
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "Database User Info:\n";
        echo "Email: " . $user['email'] . "\n";
        echo "is_admin (DB): " . ($user['is_admin'] ? '1 (true)' : '0 (false)') . "\n";
        echo "Should be admin: " . (strpos($user['email'], 'admin') !== false ? 'YES (email contains admin)' : 'NO') . "\n";
    }
}

echo "</pre>";
echo "<p><a href='/'>Go Home</a> | <a href='/login.php'>Login</a> | <a href='/admin.php'>Try Admin Again</a></p>";
?>
