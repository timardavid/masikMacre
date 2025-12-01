<?php
/**
 * API Entry Point
 * Tennis Court Booking System - REST API
 */

// Bootstrap application
require_once __DIR__ . '/bootstrap.php';

// Create router
$router = new Router();

// Register authentication middleware
$router->registerMiddleware('auth', function() {
    $authMiddleware = new AuthMiddleware();
    $authMiddleware->check();
});

// ============================================
// Public Routes (No Authentication Required)
// ============================================

// Authentication
$router->addRoute('POST', '/auth/login', 'AuthController@login');
$router->addRoute('GET', '/auth/me', 'AuthController@me', ['auth']);

// Courts
$router->addRoute('GET', '/courts', 'CourtController@index');
$router->addRoute('GET', '/courts/{id}', 'CourtController@show');
$router->addRoute('GET', '/courts/{id}/availability', 'CourtController@availability');
$router->addRoute('GET', '/surfaces', 'CourtController@surfaces');

// Bookings (public creation, but listing may require auth)
$router->addRoute('POST', '/bookings', 'BookingController@create');
$router->addRoute('GET', '/bookings/availability', 'BookingController@checkAvailability');
$router->addRoute('GET', '/bookings/calculate-price', 'BookingController@calculatePrice');

// Pricing
$router->addRoute('GET', '/pricing/rules', 'PricingController@index');
$router->addRoute('GET', '/pricing/rules/court/{courtId}', 'PricingController@courtRules');
$router->addRoute('GET', '/pricing/calculate', 'PricingController@calculate');

// ============================================
// Protected Routes (Authentication Required)
// ============================================

// Bookings (with auth)
$router->addRoute('GET', '/bookings', 'BookingController@index', ['auth']);
$router->addRoute('GET', '/bookings/{id}', 'BookingController@show', ['auth']);
$router->addRoute('PUT', '/bookings/{id}', 'BookingController@update', ['auth']);
$router->addRoute('DELETE', '/bookings/{id}', 'BookingController@cancel', ['auth']);

// Health check
$router->addRoute('GET', '/health', function() {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'API is running',
        'version' => APP_VERSION,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_UNESCAPED_UNICODE);
});

// Dispatch request
$router->dispatch();


