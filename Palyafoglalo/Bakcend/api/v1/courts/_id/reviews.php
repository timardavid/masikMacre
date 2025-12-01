<?php
/**
 * Court Reviews Endpoint
 * Handles /api/v1/courts/{id}/reviews
 */
require_once __DIR__ . '/../../../../bootstrap.php';

// Extract court ID from URL
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$pathParts = explode('/', trim(parse_url($requestUri, PHP_URL_PATH), '/'));

$courtId = null;
$courtsIndex = array_search('courts', $pathParts);

if ($courtsIndex !== false && isset($pathParts[$courtsIndex + 1])) {
    $courtId = $pathParts[$courtsIndex + 1];
    $courtId = str_replace('.php', '', $courtId);
}

if (!$courtId || !is_numeric($courtId)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Valid court ID is required'], JSON_UNESCAPED_UNICODE);
    exit;
}

$controller = new CourtReviewController();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'POST') {
    $controller->create($courtId);
} else {
    $controller->index($courtId);
}

