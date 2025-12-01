<?php
/**
 * Court-Specific Pricing Rules Endpoint
 * GET /api/v1/pricing/rules/court/{courtId}
 */
require_once __DIR__ . '/../../../../bootstrap.php';

// Extract courtId from URL path or query
$courtId = $_GET['courtId'] ?? null;

if (!$courtId) {
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $pathParts = explode('/', trim(parse_url($requestUri, PHP_URL_PATH), '/'));
    
    $courtIndex = array_search('court', $pathParts);
    if ($courtIndex !== false && isset($pathParts[$courtIndex + 1])) {
        $courtId = str_replace('.php', '', $pathParts[$courtIndex + 1]);
    }
}

if (!$courtId || !is_numeric($courtId)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Valid court ID is required'], JSON_UNESCAPED_UNICODE);
    exit;
}

$controller = new PricingController();
$controller->courtRules($courtId);

