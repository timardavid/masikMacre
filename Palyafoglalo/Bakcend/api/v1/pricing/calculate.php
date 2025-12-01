<?php
/**
 * Price Calculation Endpoint
 * GET /api/v1/pricing/calculate
 */
require_once __DIR__ . '/../../../bootstrap.php';

$controller = new PricingController();
$controller->calculate();

