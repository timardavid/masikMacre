<?php
/**
 * Get Current User Endpoint
 * GET /api/v1/auth/me
 */
require_once __DIR__ . '/../../../bootstrap.php';

$controller = new AuthController();
$controller->me();

