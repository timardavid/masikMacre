<?php
/**
 * Reviews Router
 * Routes requests to appropriate review endpoints
 */
require_once __DIR__ . '/../../../bootstrap.php';

$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$pathParts = explode('/', trim(parse_url($requestUri, PHP_URL_PATH), '/'));

// Check if it's a stats request (via query parameter or path)
$isStatsRequest = false;

// Check query parameter
if (isset($_GET['stats']) || isset($_GET['court_id'])) {
    $isStatsRequest = true;
}

// Check URL path
$reviewsIndex = array_search('reviews', $pathParts);
if ($reviewsIndex !== false) {
    $nextPart = isset($pathParts[$reviewsIndex + 1]) ? $pathParts[$reviewsIndex + 1] : null;
    if ($nextPart === 'stats' || $nextPart === 'stats.php') {
        $isStatsRequest = true;
    }
}

if ($isStatsRequest) {
    // Route to stats endpoint
    require_once __DIR__ . '/stats.php';
    exit;
}

// Otherwise route to review ID handler
require_once __DIR__ . '/_id.php';

