<?php

require_once __DIR__ . '/../db_connection.php';
require_once __DIR__ . '/../Service/staff_member_service.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';

try {
    $service = new StaffMemberService();

    switch ($action) {
        case 'getAll':
            $onlyActive = isset($_GET['active']) ? (bool)$_GET['active'] : true;
            $data = $service->getAll($onlyActive);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Ismeretlen action'], JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
