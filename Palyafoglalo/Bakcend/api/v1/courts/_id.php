<?php
/**
 * Court Detail Endpoint Template
 * This handles /api/v1/courts/{id} when accessed via .htaccess rewrite
 * OR can be copied to courts/{id}.php for each specific court
 */
require_once __DIR__ . '/../../../bootstrap.php';

// Extract ID from URL path
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$pathParts = explode('/', trim(parse_url($requestUri, PHP_URL_PATH), '/'));

// Find 'courts' in path and get next part as ID
$courtsIndex = array_search('courts', $pathParts);
if ($courtsIndex === false) {
    $courtsIndex = array_search('api', $pathParts);
    if ($courtsIndex !== false && isset($pathParts[$courtsIndex + 2]) && $pathParts[$courtsIndex + 2] === 'courts') {
        $courtsIndex = $courtsIndex + 2;
    }
}

$id = null;

// First try query parameter
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} elseif ($courtsIndex !== false && isset($pathParts[$courtsIndex + 1])) {
    $id = $pathParts[$courtsIndex + 1];
    // Remove .php extension if present
    $id = str_replace('.php', '', $id);
}

if (!$id || !is_numeric($id)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Valid court ID is required'], JSON_UNESCAPED_UNICODE);
    exit;
}

$controller = new CourtController();

// Check if availability action
if (isset($_GET['action']) && $_GET['action'] === 'availability') {
    $startDate = $_GET['start'] ?? date('Y-m-d');
    $endDate = $_GET['end'] ?? date('Y-m-d', strtotime('+30 days'));
    $controller->availability($id, $startDate, $endDate);
} else {
    $controller->show($id);
}

