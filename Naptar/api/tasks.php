<?php
require_once '../config.php';

$conn = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Teendők lekérése dátum alapján
        $date = $_GET['date'] ?? null;
        
        if ($date) {
            // Egy adott nap teendői
            $stmt = $conn->prepare("SELECT id, task_text, completed, created_at FROM tasks WHERE task_date = ? ORDER BY created_at ASC");
            $stmt->bind_param("s", $date);
        } else {
            // Összes teendő
            $stmt = $conn->prepare("SELECT id, task_date, task_text, completed, created_at FROM tasks ORDER BY task_date ASC, created_at ASC");
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $tasks = [];
        while ($row = $result->fetch_assoc()) {
            $tasks[] = [
                'id' => (int)$row['id'],
                'date' => $row['task_date'] ?? $date,
                'text' => $row['task_text'],
                'completed' => (bool)$row['completed'],
                'createdAt' => $row['created_at']
            ];
        }
        
        $stmt->close();
        sendJSON(['tasks' => $tasks]);
        break;
        
    case 'POST':
        // Új teendő létrehozása
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['date']) || !isset($data['text']) || empty(trim($data['text']))) {
            handleError('A dátum és a teendő szövege kötelező!');
        }
        
        $date = $data['date'];
        $text = trim($data['text']);
        
        $stmt = $conn->prepare("INSERT INTO tasks (task_date, task_text) VALUES (?, ?)");
        $stmt->bind_param("ss", $date, $text);
        
        if ($stmt->execute()) {
            $taskId = $conn->insert_id;
            sendJSON([
                'success' => true,
                'task' => [
                    'id' => $taskId,
                    'date' => $date,
                    'text' => $text,
                    'completed' => false
                ]
            ], 201);
        } else {
            handleError('Hiba a teendő létrehozásakor: ' . $stmt->error, 500);
        }
        
        $stmt->close();
        break;
        
    case 'PUT':
        // Teendő frissítése
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id']) || !isset($data['text'])) {
            handleError('Az ID és a szöveg kötelező!');
        }
        
        $id = (int)$data['id'];
        $text = trim($data['text']);
        $completed = isset($data['completed']) ? (int)$data['completed'] : null;
        
        if ($completed !== null) {
            // Csak completed státusz változtatás
            $stmt = $conn->prepare("UPDATE tasks SET completed = ? WHERE id = ?");
            $stmt->bind_param("ii", $completed, $id);
        } else {
            // Szöveg frissítés
            $stmt = $conn->prepare("UPDATE tasks SET task_text = ? WHERE id = ?");
            $stmt->bind_param("si", $text, $id);
        }
        
        if ($stmt->execute()) {
            sendJSON(['success' => true]);
        } else {
            handleError('Hiba a teendő frissítésénél: ' . $stmt->error, 500);
        }
        
        $stmt->close();
        break;
        
    case 'DELETE':
        // Teendő törlése
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            handleError('Az ID kötelező!');
        }
        
        $id = (int)$id;
        $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            sendJSON(['success' => true]);
        } else {
            handleError('Hiba a teendő törlésénél: ' . $stmt->error, 500);
        }
        
        $stmt->close();
        break;
        
    default:
        handleError('Nem támogatott HTTP metódus', 405);
}

$conn->close();


