<?php
require_once __DIR__ . '/../db_connection.php';
require_once __DIR__ . '/../Service/eves_program_service.php';

header('Content-Type: application/json; charset=utf-8');

$year = $_GET['year'] ?? '2025/2026';
$grouped = isset($_GET['grouped']) && $_GET['grouped'] == '1';

$svc = new EvesProgramService($pdo);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if ($grouped) {
        echo json_encode($svc->getGroupedPretty($year), JSON_UNESCAPED_UNICODE);
    } else {
        $rows = array_map(fn($x) => $x->toArray(), $svc->getByYear($year));
        echo json_encode($rows, JSON_UNESCAPED_UNICODE);
    }
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $req = ['school_year','title'];
    foreach ($req as $k) {
        if (empty($data[$k])) {
            http_response_code(422);
            echo json_encode(['error' => "Hiányzó mező: $k"], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
    try {
        $svc->add($data);
        echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
    } catch (Throwable $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Nem támogatott metódus'], JSON_UNESCAPED_UNICODE);
