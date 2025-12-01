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
        getWorkStatus();
        break;
    case 'POST':
        updateWorkStatus();
        break;
}

function getWorkStatus() {
    global $conn;
    
    $stmt = $conn->prepare("SELECT ws.*, u.name as user_name, u.role FROM work_status ws LEFT JOIN users u ON ws.user_id = u.id ORDER BY ws.created_at DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    $statuses = [];
    
    while ($row = $result->fetch_assoc()) {
        $statuses[] = $row;
    }
    
    echo json_encode($statuses);
    $stmt->close();
}

function updateWorkStatus() {
    global $conn;
    $input = json_decode(file_get_contents('php://input'), true);
    
    $user_id = $input['user_id'] ?? $_SESSION['user_id'];
    $status = $input['status'];
    
    // End current status if exists
    $end_stmt = $conn->prepare("UPDATE work_status SET end_time = NOW() WHERE user_id = ? AND end_time IS NULL");
    $end_stmt->bind_param("i", $user_id);
    $end_stmt->execute();
    $end_stmt->close();
    
    // Insert new status
    $stmt = $conn->prepare("INSERT INTO work_status (user_id, status, start_time, notes) VALUES (?, ?, NOW(), ?)");
    $notes = $input['notes'] ?? '';
    $stmt->bind_param("iss", $user_id, $status, $notes);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    $stmt->close();
}

$conn->close();
?>
