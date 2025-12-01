<?php
/**
 * Booking Price Calculation Endpoint
 * GET /api/v1/bookings/calculate-price
 */
require_once __DIR__ . '/../../../bootstrap.php';

$controller = new BookingController();
$controller->calculatePrice();

