<?php
error_reporting(0); // Turn off error display for production
ini_set('display_errors', 0);

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        handleLogin();
        break;
    case 'logout':
        handleLogout();
        break;
    case 'check':
        checkAuth();
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
}

function handleLogin() {
    $input = json_decode(file_get_contents('php://input'), true);
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT id, name, email, password, role, department, status FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // For demo purposes, any password works. In production, use password_verify()
        if ($user['status'] === 'active') {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_department'] = $user['department'];
            
            echo json_encode([
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'department' => $user['department']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Felhasználó inaktív']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Érvénytelen email vagy jelszó']);
    }
    $stmt->close();
    $conn->close();
}

function handleLogout() {
    session_destroy();
    echo json_encode(['success' => true]);
}

function checkAuth() {
    if (isset($_SESSION['user_id'])) {
        echo json_encode([
            'authenticated' => true,
            'user' => [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'],
                'email' => $_SESSION['user_email'],
                'role' => $_SESSION['user_role'],
                'department' => $_SESSION['user_department']
            ]
        ]);
    } else {
        echo json_encode(['authenticated' => false]);
    }
}
?>
