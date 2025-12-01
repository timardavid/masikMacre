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
        getTasks();
        break;
    case 'POST':
        createTask();
        break;
    case 'PUT':
        updateTask();
        break;
    case 'DELETE':
        deleteTask();
        break;
}

function getTasks() {
    global $conn;
    $user_role = $_SESSION['user_role'];
    $user_id = $_SESSION['user_id'];
    
    // CEO and Admin can see all tasks
    if ($user_role === 'CEO' || $user_role === 'Admin') {
        $stmt = $conn->prepare("SELECT t.*, u.name as user_name FROM tasks t LEFT JOIN users u ON t.user_id = u.id ORDER BY 
            FIELD(t.priority, 'Critical', 'Very Urgent', 'Urgent', 'Not Urgent'), t.created_at DESC");
    } else {
        // Others see only their tasks
        $stmt = $conn->prepare("SELECT t.*, u.name as user_name FROM tasks t LEFT JOIN users u ON t.user_id = u.id WHERE t.user_id = ? ORDER BY 
            FIELD(t.priority, 'Critical', 'Very Urgent', 'Urgent', 'Not Urgent'), t.created_at DESC");
        $stmt->bind_param("i", $user_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $tasks = [];
    
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
    
    echo json_encode($tasks);
    $stmt->close();
}

function createTask() {
    global $conn;
    $input = json_decode(file_get_contents('php://input'), true);
    
    $stmt = $conn->prepare("INSERT INTO tasks (user_id, client_name, task_title, description, priority, status, deadline) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $input['user_id'], $input['client_name'], $input['task_title'], $input['description'], 
        $input['priority'], $input['status'], $input['deadline']);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    $stmt->close();
}

function updateTask() {
    global $conn;
    $input = json_decode(file_get_contents('php://input'), true);
    
    $stmt = $conn->prepare("UPDATE tasks SET user_id = ?, client_name = ?, task_title = ?, description = ?, priority = ?, status = ?, deadline = ? WHERE id = ?");
    $stmt->bind_param("issssssi", $input['user_id'], $input['client_name'], $input['task_title'], $input['description'], 
        $input['priority'], $input['status'], $input['deadline'], $input['id']);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    $stmt->close();
}

function deleteTask() {
    global $conn;
    $id = $_GET['id'] ?? 0;
    
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    $stmt->close();
}

$conn->close();
?>
