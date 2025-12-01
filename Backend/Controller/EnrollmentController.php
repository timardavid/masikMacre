<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../db_connection.php';
require_once __DIR__ . '/../Service/EnrollmentService.php';

$service = new EnrollmentService($pdo);

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $year = $_GET['year'] ?? date('Y');
        $enrollment = $service->getEnrollment($year);

        if ($enrollment) {
            echo json_encode(['success' => true, 'data' => $enrollment], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'Nincs adat erre az évre: ' . $year], JSON_UNESCAPED_UNICODE);
        }

    } elseif ($method === 'POST') {
        $input = json_decode(file_get_contents("php://input"), true);
        if ($service->saveEnrollment($input)) {
            echo json_encode(['success' => true, 'message' => 'Mentve/Frissítve'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'Mentés sikertelen'], JSON_UNESCAPED_UNICODE);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Nem támogatott metódus'], JSON_UNESCAPED_UNICODE);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
