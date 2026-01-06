<?php
/**
 * Router for PHP Built-in Server
 * This handles API routes when using: php -S localhost:8000 router.php
 * 
 * Usage: php -S localhost:8000 router.php
 */

$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);
$queryString = parse_url($requestUri, PHP_URL_QUERY);

// Remove leading slash
$requestPath = ltrim($requestPath, '/');

// Handle API routes
if (strpos($requestPath, 'api/') === 0) {
    // Remove 'api/' prefix
    $apiPath = substr($requestPath, 4);
    
    // Remove .php if present
    $apiPath = preg_replace('/\.php$/', '', $apiPath);
    
    // Map to actual PHP file
    $apiFile = __DIR__ . '/api/' . $apiPath . '.php';
    
    if (file_exists($apiFile)) {
        // Add query string back
        if ($queryString) {
            $_SERVER['REQUEST_URI'] = '/api/' . $apiPath . '.php?' . $queryString;
            parse_str($queryString, $_GET);
        } else {
            $_SERVER['REQUEST_URI'] = '/api/' . $apiPath . '.php';
        }
        
        require $apiFile;
        exit;
    }
}

// Handle regular PHP files
if (file_exists(__DIR__ . $requestPath) && is_file(__DIR__ . $requestPath)) {
    return false; // Serve the file as-is
}

// Handle directory requests (serve index.php)
if (is_dir(__DIR__ . '/' . $requestPath)) {
    if (file_exists(__DIR__ . '/' . $requestPath . '/index.php')) {
        require __DIR__ . '/' . $requestPath . '/index.php';
        exit;
    }
}

// Default: serve index.php for root
if ($requestPath === '' || $requestPath === '/') {
    require __DIR__ . '/index.php';
    exit;
}

// 404 - file not found
http_response_code(404);
echo "404 - File Not Found: " . htmlspecialchars($requestPath);
