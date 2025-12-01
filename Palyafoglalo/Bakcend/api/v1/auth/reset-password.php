<?php
/**
 * Reset Password Endpoint
 * POST /api/v1/auth/reset-password
 */
require_once __DIR__ . '/../../../bootstrap.php';

$controller = new AuthController();
$controller->resetPassword();

