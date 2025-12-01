<?php
/**
 * Direct API endpoint for courts
 */
require_once __DIR__ . '/../../bootstrap.php';

$controller = new CourtController();
$controller->index();
