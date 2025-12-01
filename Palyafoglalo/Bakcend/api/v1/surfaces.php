<?php
/**
 * Surfaces Endpoint
 * GET /api/v1/surfaces
 */
require_once __DIR__ . '/../../bootstrap.php';

$controller = new CourtController();
$controller->surfaces();

