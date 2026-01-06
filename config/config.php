<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('UTC');

define('BASE_URL', 'http://localhost');

require_once __DIR__ . '/database.php';
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

function requireAuth() {
    if (!isAuthenticated()) {
        jsonResponse(['message' => 'Unauthorized'], 401);
    }
}

function requireAdmin() {
    requireAuth();
    if (!isAdmin()) {
        jsonResponse(['message' => 'Forbidden'], 403);
    }
}
