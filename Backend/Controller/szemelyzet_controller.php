<?php
require_once __DIR__ . '/../Service/szemelyzet_service.php';
require_once __DIR__ . '/../db_connection.php';

$service = new SzemelyzetService($pdo);
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $grouped = isset($_GET['grouped']) && $_GET['grouped'] == '1';
    if ($grouped) {
        echo json_encode($service->getGrouped(), JSON_UNESCAPED_UNICODE);
    } else {
        $rows = array_map(fn($s) => $s->toArray(), $service->getAll());
        echo json_encode($rows, JSON_UNESCAPED_UNICODE);
    }
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = trim($data['name'] ?? '');
    $role = trim($data['role'] ?? '');

    if (mb_strlen($name) < 3) {
        http_response_code(422);
        echo json_encode(['error' => 'Név kötelező (min. 3 karakter).'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($role === '') {
        http_response_code(422);
        echo json_encode(['error' => 'Munkakör kötelező.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        $service->add($name, $role);
        echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
    } catch (Throwable $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Nem támogatott metódus'], JSON_UNESCAPED_UNICODE);
