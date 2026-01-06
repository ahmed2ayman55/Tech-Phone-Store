<?php
require_once __DIR__ . '/../config/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

switch ($method) {
    case 'GET':
        requireAuth();
        
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $userId = getCurrentUserId();
            
            $stmt = $db->prepare("
                SELECT * FROM orders 
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$id, $userId]);
            $order = $stmt->fetch();
            
            if (!$order) {
                jsonResponse(['message' => 'Order not found'], 404);
            }
            
            $stmt = $db->prepare("
                SELECT oi.*, p.name as product_name, p.image_url as product_image
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
            ");
            $stmt->execute([$id]);
            $items = $stmt->fetchAll();
            
            $order['items'] = $items;
            $order['address'] = json_decode($order['address'], true);
            
            jsonResponse($order);
        } else {
            $userId = getCurrentUserId();
            
            $stmt = $db->prepare("
                SELECT * FROM orders 
                WHERE user_id = ?
                ORDER BY created_at DESC
            ");
            $stmt->execute([$userId]);
            $orders = $stmt->fetchAll();
            
            foreach ($orders as &$order) {
                $stmt = $db->prepare("
                    SELECT oi.*, p.name as product_name, p.image_url as product_image
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = ?
                ");
                $stmt->execute([$order['id']]);
                $order['items'] = $stmt->fetchAll();
                $order['address'] = json_decode($order['address'], true);
            }
            
            jsonResponse($orders);
        }
        break;
        
    case 'POST':
        requireAuth();
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['items']) || !isset($data['address'])) {
            jsonResponse(['message' => 'Missing required fields'], 400);
        }
        
        $userId = getCurrentUserId();
        $items = $data['items'];
        $address = $data['address'];
        
        $total = 0;
        foreach ($items as $item) {
            $stmt = $db->prepare("SELECT price FROM products WHERE id = ?");
            $stmt->execute([$item['product_id']]);
            $product = $stmt->fetch();
            
            if (!$product) {
                jsonResponse(['message' => "Product {$item['product_id']} not found"], 400);
            }
            
                $total += (float)$product['price'] * (int)$item['quantity'];
            }
            
            $db->beginTransaction();
        try {
            $stmt = $db->prepare("
                INSERT INTO orders (user_id, total, address, status)
                VALUES (?, ?, ?, 'pending')
            ");
            $stmt->execute([
                $userId,
                $total,
                json_encode($address)
            ]);
            
            $orderId = $db->lastInsertId();
            
            foreach ($items as $item) {
                $stmt = $db->prepare("SELECT price FROM products WHERE id = ?");
                $stmt->execute([$item['product_id']]);
                $product = $stmt->fetch();
                
                $stmt = $db->prepare("
                    INSERT INTO order_items (order_id, product_id, quantity, price)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['quantity'],
                    $product['price']
                ]);
            }
            
            $db->commit();
            
            $stmt = $db->prepare("SELECT * FROM orders WHERE id = ?");
            $stmt->execute([$orderId]);
            $order = $stmt->fetch();
            
            $stmt = $db->prepare("
                SELECT oi.*, p.name as product_name, p.image_url as product_image
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
            ");
            $stmt->execute([$orderId]);
            $order['items'] = $stmt->fetchAll();
            $order['address'] = json_decode($order['address'], true);
            
            jsonResponse($order, 201);
        } catch (Exception $e) {
            $db->rollBack();
            jsonResponse(['message' => 'Failed to create order'], 500);
        }
        break;
        
    default:
        jsonResponse(['message' => 'Method not allowed'], 405);
}
