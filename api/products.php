<?php
require_once __DIR__ . '/../config/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch();
            
            if (!$product) {
                jsonResponse(['message' => 'Product not found'], 404);
            }
            
            if ($product['specs']) {
                $product['specs'] = json_decode($product['specs'], true);
            }
            
            jsonResponse($product);
        } else {
            $search = $_GET['search'] ?? '';
            $category = $_GET['category'] ?? '';
            
            $sql = "SELECT * FROM products WHERE 1=1";
            $params = [];
            
            if ($search) {
                $sql .= " AND name LIKE ?";
                $params[] = "%$search%";
            }
            
            if ($category) {
                $sql .= " AND category = ?";
                $params[] = $category;
            }
            
            $sql .= " ORDER BY id DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $products = $stmt->fetchAll();
            
            foreach ($products as &$product) {
                if ($product['specs']) {
                    $product['specs'] = json_decode($product['specs'], true);
                }
            }
            
            jsonResponse($products);
        }
        break;
        
    case 'POST':
        requireAdmin();
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['name']) || !isset($data['description']) || !isset($data['price']) || 
            !isset($data['category']) || !isset($data['image_url'])) {
            jsonResponse(['message' => 'Missing required fields'], 400);
        }
        
        $specs = isset($data['specs']) ? json_encode($data['specs']) : null;
        $stock = $data['stock'] ?? 0;
        
        $stmt = $db->prepare("
            INSERT INTO products (name, description, price, category, image_url, stock, specs)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['name'],
            $data['description'],
            $data['price'],
            $data['category'],
            $data['image_url'],
            $stock,
            $specs
        ]);
        
        $productId = $db->lastInsertId();
        $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        
        if ($product['specs']) {
            $product['specs'] = json_decode($product['specs'], true);
        }
        
        jsonResponse($product, 201);
        break;
        
    case 'PUT':
        requireAdmin();
        
        if (!isset($_GET['id'])) {
            jsonResponse(['message' => 'Product ID required'], 400);
        }
        
        $id = (int)$_GET['id'];
        $data = json_decode(file_get_contents('php://input'), true);
        
        $fields = [];
        $params = [];
        
        $allowedFields = ['name', 'description', 'price', 'category', 'image_url', 'stock', 'specs'];
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                if ($field === 'specs') {
                    $fields[] = "$field = ?";
                    $params[] = json_encode($data[$field]);
                } else {
                    $fields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
        }
        
        if (empty($fields)) {
            jsonResponse(['message' => 'No fields to update'], 400);
        }
        
        $params[] = $id;
        $sql = "UPDATE products SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        
        $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        
        if ($product['specs']) {
            $product['specs'] = json_decode($product['specs'], true);
        }
        
        jsonResponse($product);
        break;
        
    case 'DELETE':
        requireAdmin();
        
        if (!isset($_GET['id'])) {
            jsonResponse(['message' => 'Product ID required'], 400);
        }
        
        $id = (int)$_GET['id'];
        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        
        http_response_code(204);
        exit;
        break;
        
    default:
        jsonResponse(['message' => 'Method not allowed'], 405);
}
