<?php
/**
 * Portfolio API Endpoint
 * JDW
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../Model/Portfolio.php';

try {
    $portfolio = new Portfolio();
    
    // Check if specific item requested
    if (isset($_GET['id'])) {
        $data = $portfolio->getById($_GET['id']);
        
        if (!$data) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Portfolio item not found'
            ]);
            exit;
        }
    } elseif (isset($_GET['category'])) {
        $data = $portfolio->getByCategory($_GET['category']);
    } elseif (isset($_GET['featured'])) {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
        $data = $portfolio->getFeatured($limit);
    } else {
        // Get all portfolio items
        $data = $portfolio->getAll();
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
    error_log("Portfolio API error: " . $e->getMessage());
}

