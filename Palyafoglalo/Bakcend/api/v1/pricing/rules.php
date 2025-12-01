<?php
/**
 * Pricing Rules Endpoint
 * GET /api/v1/pricing/rules
 */
require_once __DIR__ . '/../../../bootstrap.php';

$controller = new PricingController();
$controller->index();

