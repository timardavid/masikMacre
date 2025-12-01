<?php
/**
 * Registration Endpoint
 * POST /api/v1/auth/register
 */
require_once __DIR__ . '/../../../bootstrap.php';

$controller = new AuthController();
$controller->register();

