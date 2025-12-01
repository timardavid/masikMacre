<?php
/**
 * Booking Detail/Update/Delete Endpoint
 * GET /api/v1/bookings/{id} - Get booking
 * PUT /api/v1/bookings/{id} - Update booking  
 * DELETE /api/v1/bookings/{id} - Cancel booking
 */
require_once __DIR__ . '/../../../bootstrap.php';

// Extract ID from URL path
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$pathParts = explode('/', trim(parse_url($requestUri, PHP_URL_PATH), '/'));

// Find 'bookings' in path and get next part as ID
$bookingsIndex = array_search('bookings', $pathParts);
if ($bookingsIndex === false) {
    $bookingsIndex = array_search('api', $pathParts);
    if ($bookingsIndex !== false && isset($pathParts[$bookingsIndex + 2]) && $pathParts[$bookingsIndex + 2] === 'bookings') {
        $bookingsIndex = $bookingsIndex + 2;
    }
}

$id = null;

// First try query parameter
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} elseif ($bookingsIndex !== false && isset($pathParts[$bookingsIndex + 1])) {
    $id = $pathParts[$bookingsIndex + 1];
    // Remove .php extension if present
    $id = str_replace('.php', '', $id);
}

if (!$id || !is_numeric($id)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Valid booking ID is required'], JSON_UNESCAPED_UNICODE);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$controller = new BookingController();

if ($method === 'GET') {
    $controller->show($id);
} elseif ($method === 'PUT') {
    $controller->update($id);
} elseif ($method === 'DELETE') {
    $controller->cancel($id);
} else {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
}

