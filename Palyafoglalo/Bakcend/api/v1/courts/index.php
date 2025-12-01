<?php
/**
 * Court Detail Endpoint
 * GET /api/v1/courts/{id}
 */
require_once __DIR__ . '/../../../../bootstrap.php';

// Get ID from URL path
$pathParts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$idIndex = array_search('courts', $pathParts);
$id = $idIndex !== false && isset($pathParts[$idIndex + 1]) ? $pathParts[$idIndex + 1] : null;

if (!$id) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Court ID is required'], JSON_UNESCAPED_UNICODE);
    exit;
}

$controller = new CourtController();
$controller->show($id);

