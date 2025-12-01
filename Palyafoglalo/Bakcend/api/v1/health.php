<?php
/**
 * Health Check Endpoint
 * GET /api/v1/health
 */
require_once __DIR__ . '/../../bootstrap.php';

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'API is running',
    'version' => APP_VERSION,
    'timestamp' => date('Y-m-d H:i:s')
], JSON_UNESCAPED_UNICODE);

