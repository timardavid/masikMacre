<?php
/**
 * Forgot Password Endpoint
 * POST /api/v1/auth/forgot-password
 */
require_once __DIR__ . '/../../../bootstrap.php';

$controller = new AuthController();
$controller->forgotPassword();

