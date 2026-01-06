<?php
/**
 * Test API Endpoints
 * Visit: http://localhost:8000/test-api.php
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>API Test</title>
    <style>
        body { font-family: monospace; padding: 20px; }
        .test { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background: #d4edda; border-color: #c3e6cb; }
        .error { background: #f8d7da; border-color: #f5c6cb; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>API Endpoint Test</h1>
    
    <?php
    $baseUrl = 'http://localhost:8000';
    $tests = [
        'Products List' => $baseUrl . '/api/products.php',
        'Auth Check' => $baseUrl . '/api/auth.php',
    ];
    
    foreach ($tests as $name => $url) {
        echo "<div class='test'>";
        echo "<h3>$name</h3>";
        echo "<p><strong>URL:</strong> <a href='$url' target='_blank'>$url</a></p>";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);
        
        $headers = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        
        echo "<p><strong>HTTP Code:</strong> $httpCode</p>";
        
        // Check if it's JSON
        $isJson = json_decode($body) !== null;
        $contentType = 'text/html';
        if (preg_match('/Content-Type:\s*([^;\s]+)/i', $headers, $matches)) {
            $contentType = trim($matches[1]);
        }
        
        echo "<p><strong>Content-Type:</strong> $contentType</p>";
        echo "<p><strong>Is JSON:</strong> " . ($isJson ? '✅ Yes' : '❌ No') . "</p>";
        
        if ($isJson) {
            echo "<div class='success'>";
            echo "<strong>Response:</strong>";
            echo "<pre>" . htmlspecialchars(json_encode(json_decode($body), JSON_PRETTY_PRINT)) . "</pre>";
            echo "</div>";
        } else {
            echo "<div class='error'>";
            echo "<strong>Response (not JSON):</strong>";
            echo "<pre>" . htmlspecialchars(substr($body, 0, 500)) . "</pre>";
            echo "</div>";
        }
        
        echo "</div>";
    }
    ?>
    
    <div class="test">
        <h3>Manual Test</h3>
        <p>Open browser console (F12) and run:</p>
        <pre>fetch('/api/products.php').then(r => r.json()).then(console.log)</pre>
    </div>
</body>
</html>
