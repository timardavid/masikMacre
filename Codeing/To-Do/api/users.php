<?php
error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/../config.php';

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');
$method = $_SERVER['REQUEST_METHOD'];
$conn = getDBConnection();

switch ($method) {
    case 'GET':
        getUsers();
        break;
    case 'POST':
        createUser();
        break;
    case 'PUT':
        updateUser();
        break;
    case 'DELETE':
        deleteUser();
        break;
}

function getUsers() {
    global $conn;
    
    $stmt = $conn->prepare("SELECT id, name, email, role, department, phone, salary, status, created_at FROM users ORDER BY name");
    $stmt->execute();
    $result = $stmt->get_result();
    $users = [];
    
    while ($row = $result->fetch_assoc()) {
        // Get current work status
        $status_stmt = $conn->prepare("SELECT status FROM work_status WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
        $status_stmt->bind_param("i", $row['id']);
        $status_stmt->execute();
        $status_result = $status_stmt->get_result();
        $row['current_status'] = $status_result->num_rows > 0 ? $status_result->fetch_assoc()['status'] : 'no_work';
        $status_stmt->close();
        
        $users[] = $row;
    }
    
    echo json_encode($users);
    $stmt->close();
}

function createUser() {
    global $conn;
    $input = json_decode(file_get_contents('php://input'), true);
    
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, department, phone, salary) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $password = password_hash($input['password'], PASSWORD_DEFAULT);
    $salary = $input['salary'] ?? 0;
    $stmt->bind_param("ssssssd", $input['name'], $input['email'], $password, $input['role'], $input['department'], $input['phone'], $salary);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    $stmt->close();
}

function updateUser() {
    global $conn;
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['password'])) {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ?, role = ?, department = ?, phone = ?, salary = ?, status = ? WHERE id = ?");
        $password = password_hash($input['password'], PASSWORD_DEFAULT);
        $salary = $input['salary'] ?? 0;
        $stmt->bind_param("ssssssdsi", $input['name'], $input['email'], $password, $input['role'], $input['department'], $input['phone'], $salary, $input['status'], $input['id']);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ?, department = ?, phone = ?, salary = ?, status = ? WHERE id = ?");
        $salary = $input['salary'] ?? 0;
        $stmt->bind_param("sssssdsi", $input['name'], $input['email'], $input['role'], $input['department'], $input['phone'], $salary, $input['status'], $input['id']);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    $stmt->close();
}

function deleteUser() {
    global $conn;
    $id = $_GET['id'] ?? 0;
    
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
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
