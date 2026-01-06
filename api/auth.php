<?php
require_once __DIR__ . '/../config/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

switch ($method) {
    case 'POST':
        $action = $_GET['action'] ?? '';
        
        if ($action === 'admin-login') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['email']) || !isset($data['password'])) {
                jsonResponse(['message' => 'Email and password required'], 400);
            }
            
            $email = $data['email'];
            $password = $data['password'];
            
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND is_admin = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if (!$user || !password_verify($password, $user['password_hash'])) {
                jsonResponse(['message' => 'Invalid credentials or insufficient privileges'], 401);
            }
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['is_admin'] = true;
            
            jsonResponse([
                'user' => [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'is_admin' => true
                ]
            ]);
            
        } elseif ($action === 'register') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['email']) || !isset($data['password'])) {
                jsonResponse(['message' => 'Email and password required'], 400);
            }
            
            $email = $data['email'];
            $password = $data['password'];
            $firstName = $data['first_name'] ?? '';
            $lastName = $data['last_name'] ?? '';
            
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                jsonResponse(['message' => 'Email already registered'], 400);
            }
            
            $userId = uniqid('user_', true);
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $isAdmin = false;
            
            $stmt = $db->prepare("
                INSERT INTO users (id, email, first_name, last_name, password_hash, is_admin)
                VALUES (?, ?, ?, ?, ?, 0)
            ");
            $stmt->execute([$userId, $email, $firstName, $lastName, $passwordHash]);
            
            $_SESSION['user_id'] = $userId;
            $_SESSION['email'] = $email;
            $_SESSION['first_name'] = $firstName;
            $_SESSION['last_name'] = $lastName;
            $_SESSION['is_admin'] = $isAdmin;
            
            jsonResponse([
                'user' => [
                    'id' => $userId,
                    'email' => $email,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'is_admin' => $isAdmin
                ]
            ], 201);
            
        } elseif ($action === 'login') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['email']) || !isset($data['password'])) {
                jsonResponse(['message' => 'Email and password required'], 400);
            }
            
            $email = $data['email'];
            $password = $data['password'];
            
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND is_admin = 0");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if (!$user || !password_verify($password, $user['password_hash'])) {
                jsonResponse(['message' => 'Invalid credentials'], 401);
            }
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['is_admin'] = false;
            
            jsonResponse([
                'user' => [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'is_admin' => false
                ]
            ]);
            
        } elseif ($action === 'logout') {
            session_destroy();
            jsonResponse(['message' => 'Logged out successfully']);
            
        } else {
            jsonResponse(['message' => 'Invalid action'], 400);
        }
        break;
        
    case 'GET':
        if (isAuthenticated()) {
            jsonResponse([
                'user' => [
                    'id' => $_SESSION['user_id'],
                    'email' => $_SESSION['email'],
                    'first_name' => $_SESSION['first_name'],
                    'last_name' => $_SESSION['last_name'],
                    'is_admin' => $_SESSION['is_admin'] ?? false
                ]
            ]);
        } else {
            jsonResponse(['user' => null]);
        }
        break;
        
    default:
        jsonResponse(['message' => 'Method not allowed'], 405);
}
