<?php
/**
 * Court Review Stats Endpoint
 * Handles /api/v1/reviews/stats?court_id={id}
 * OR /api/v1/courts/{id}/reviews/stats
 */
require_once __DIR__ . '/../../../bootstrap.php';

// Extract court ID from query parameter or URL path
$courtId = null;

// First try query parameter
if (isset($_GET['court_id']) && is_numeric($_GET['court_id'])) {
    $courtId = intval($_GET['court_id']);
} else {
    // Try to extract from URL path
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $pathParts = explode('/', trim(parse_url($requestUri, PHP_URL_PATH), '/'));
    
    $courtsIndex = array_search('courts', $pathParts);
    if ($courtsIndex !== false && isset($pathParts[$courtsIndex + 1])) {
        $potentialId = $pathParts[$courtsIndex + 1];
        $potentialId = str_replace('.php', '', $potentialId);
        if (is_numeric($potentialId)) {
            $courtId = intval($potentialId);
        }
    }
}

if (!$courtId || !is_numeric($courtId)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => 'Valid court ID is required'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$controller = new CourtReviewController();
$controller->stats($courtId);

