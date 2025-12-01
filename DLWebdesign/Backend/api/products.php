<?php
/**
 * Products API Endpoint (Pricing Packages)
 * JDW
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../Model/Products.php';

try {
    $products = new Products();
    
    // Check if specific product requested
    if (isset($_GET['slug'])) {
        $data = $products->getBySlug($_GET['slug']);
        
        if (!$data) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Product not found'
            ]);
            exit;
        }
    } elseif (isset($_GET['id'])) {
        $data = $products->getById($_GET['id']);
        
        if (!$data) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Product not found'
            ]);
            exit;
        }
    } elseif (isset($_GET['featured'])) {
        $data = $products->getFeatured();
    } else {
        // Get all products
        $data = $products->getAll();
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
    error_log("Products API error: " . $e->getMessage());
}

