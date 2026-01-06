<?php
require_once __DIR__ . '/../config/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

$productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : null;

switch ($method) {
    case 'GET':
        if (!$productId) {
            jsonResponse(['message' => 'Product ID required'], 400);
        }
        
        $stmt = $db->prepare("
            SELECT r.*, u.first_name, u.last_name
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.product_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$productId]);
        $reviews = $stmt->fetchAll();
        
        jsonResponse($reviews);
        break;
        
    case 'POST':
        requireAuth();
        
        if (!$productId) {
            jsonResponse(['message' => 'Product ID required'], 400);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['rating']) || !isset($data['comment'])) {
            jsonResponse(['message' => 'Missing required fields'], 400);
        }
        
        $userId = getCurrentUserId();
        $rating = (int)$data['rating'];
        $comment = $data['comment'];
        
        if ($rating < 1 || $rating > 5) {
            jsonResponse(['message' => 'Rating must be between 1 and 5'], 400);
        }
        
        $stmt = $db->prepare("
            INSERT INTO reviews (user_id, product_id, rating, comment)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$userId, $productId, $rating, $comment]);
        
        $reviewId = $db->lastInsertId();
        $stmt = $db->prepare("
            SELECT r.*, u.first_name, u.last_name
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.id = ?
        ");
        $stmt->execute([$reviewId]);
        $review = $stmt->fetch();
        
        jsonResponse($review, 201);
        break;
        
    default:
        jsonResponse(['message' => 'Method not allowed'], 405);
}
