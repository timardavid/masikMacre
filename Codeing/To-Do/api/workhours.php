<?php
error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');
$method = $_SERVER['REQUEST_METHOD'];
$conn = getDBConnection();

switch ($method) {
    case 'GET':
        getWorkHours();
        break;
    case 'POST':
        logWorkHours();
        break;
    case 'PUT':
        updateWorkHours();
        break;
}

function getWorkHours() {
    global $conn;
    $user_id = $_GET['user_id'] ?? $_SESSION['user_id'];
    $month = $_GET['month'] ?? date('Y-m');
    
    $stmt = $conn->prepare("SELECT * FROM work_hours WHERE user_id = ? AND DATE_FORMAT(date, '%Y-%m') = ? ORDER BY date DESC");
    $stmt->bind_param("is", $user_id, $month);
    $stmt->execute();
    $result = $stmt->get_result();
    $hours = [];
    
    while ($row = $result->fetch_assoc()) {
        $hours[] = $row;
    }
    
    echo json_encode($hours);
    $stmt->close();
}

function logWorkHours() {
    global $conn;
    $input = json_decode(file_get_contents('php://input'), true);
    
    $user_id = $input['user_id'] ?? $_SESSION['user_id'];
    $date = $input['date'] ?? date('Y-m-d');
    $hours = $input['hours_worked'] ?? 8;
    $notes = $input['notes'] ?? '';
    
    // Use INSERT ... ON DUPLICATE KEY UPDATE
    $stmt = $conn->prepare("INSERT INTO work_hours (user_id, date, hours_worked, notes) VALUES (?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE hours_worked = ?, notes = ?");
    $stmt->bind_param("isdsds", $user_id, $date, $hours, $notes, $hours, $notes);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    $stmt->close();
}

function updateWorkHours() {
    global $conn;
    $input = json_decode(file_get_contents('php://input'), true);
    
    $stmt = $conn->prepare("UPDATE work_hours SET hours_worked = ?, notes = ? WHERE user_id = ? AND date = ?");
    $stmt->bind_param("dsis", $input['hours_worked'], $input['notes'], $input['user_id'], $input['date']);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    $stmt->close();
}

$conn->close();
?>
