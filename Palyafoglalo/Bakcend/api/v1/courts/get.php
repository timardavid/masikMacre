<?php
/**
 * Court Detail Endpoint
 * GET /api/v1/courts/{id}
 * This file handles courts/{id} requests via URL rewriting or direct call
 */
require_once __DIR__ . '/../../../bootstrap.php';

// Get ID from query parameter or URL path
$id = $_GET['id'] ?? null;

if (!$id) {
    // Try to extract from URL
    $pathParts = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
    $idIndex = array_search('courts', $pathParts);
    if ($idIndex !== false && isset($pathParts[$idIndex + 1])) {
        $id = $pathParts[$idIndex + 1];
    }
}

if (!$id) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Court ID is required'], JSON_UNESCAPED_UNICODE);
    exit;
}

$controller = new CourtController();
$controller->show($id);

