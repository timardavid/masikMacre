<?php
/**
 * Booking Availability Check Endpoint
 * GET /api/v1/bookings/availability
 */
require_once __DIR__ . '/../../../bootstrap.php';

$controller = new BookingController();
$controller->checkAvailability();

