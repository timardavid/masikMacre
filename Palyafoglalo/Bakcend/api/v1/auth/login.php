<?php
/**
 * Login Endpoint
 * POST /api/v1/auth/login
 */
require_once __DIR__ . '/../../../bootstrap.php';

$controller = new AuthController();
$controller->login();

