<?php
/**
 * Bookings Endpoint
 * GET /api/v1/bookings - List bookings (public if filtered by court_id)
 * POST /api/v1/bookings - Create booking
 */
require_once __DIR__ . '/../../bootstrap.php';

$controller = new BookingController();

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// For GET requests with court_id filter, allow public access
if ($method === 'GET') {
    // Check if it's a public query (filtered by court_id for availability)
    $hasCourtFilter = isset($_GET['court_id']) && !empty($_GET['court_id']);
    
    if ($hasCourtFilter) {
        // Public access for availability checking
        $controller->index();
    } else {
        // Otherwise require auth (user's own bookings)
        $controller->index();
    }
} elseif ($method === 'POST') {
    // Require authentication for creating bookings
    $authMiddleware = new AuthMiddleware();
    $authMiddleware->check();
    $controller->create();
} else {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
}

