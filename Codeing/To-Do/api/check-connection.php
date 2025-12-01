<?php
/**
 * API Connection Test
 * Tests if API works correctly
 */

require_once '../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $conn = getDBConnection();
    
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    $data = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'message' => 'Database connection successful',
        'users_count' => $data['count'],
        'php_version' => PHP_VERSION
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'php_version' => PHP_VERSION
    ]);
}
?>
