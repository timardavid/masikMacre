<?php
/**
 * Categories API Endpoint
 * JDW
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../Model/Categories.php';

try {
    $categories = new Categories();
    
    // Check if specific category requested
    if (isset($_GET['slug'])) {
        $data = $categories->getBySlug($_GET['slug']);
        
        if (!$data) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Category not found'
            ]);
            exit;
        }
    } elseif (isset($_GET['id'])) {
        $data = $categories->getById($_GET['id']);
        
        if (!$data) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Category not found'
            ]);
            exit;
        }
    } else {
        // Get all categories
        $data = $categories->getAll();
    }
    
    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error occurred'
    ]);
    error_log("Categories API error: " . $e->getMessage());
}

