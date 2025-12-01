<?php
/**
 * Review Update/Delete Endpoint
 * Handles /api/v1/reviews/{id}
 */
require_once __DIR__ . '/../../../bootstrap.php';

// Extract review ID from URL
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$pathParts = explode('/', trim(parse_url($requestUri, PHP_URL_PATH), '/'));

$reviewId = null;
$reviewsIndex = array_search('reviews', $pathParts);

if ($reviewsIndex !== false && isset($pathParts[$reviewsIndex + 1])) {
    $reviewId = $pathParts[$reviewsIndex + 1];
    $reviewId = str_replace('.php', '', $reviewId);
}

if (!$reviewId || !is_numeric($reviewId)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Valid review ID is required'], JSON_UNESCAPED_UNICODE);
    exit;
}

$controller = new CourtReviewController();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'PUT') {
    $controller->update($reviewId);
} elseif ($method === 'DELETE') {
    $controller->delete($reviewId);
} else {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
}

