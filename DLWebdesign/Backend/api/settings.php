<?php
/**
 * Settings API Endpoint
 * JDW
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../Model/Settings.php';

try {
    $settings = new Settings();
    
    // Get all settings as associative array
    $data = $settings->getAsArray();
    
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
    error_log("Settings API error: " . $e->getMessage());
}

