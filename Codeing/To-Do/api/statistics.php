<?php
error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');
$conn = getDBConnection();

// Get current month statistics
$user_id = $_GET['user_id'] ?? $_SESSION['user_id'];
$month = $_GET['month'] ?? date('Y-m');

$stmt = $conn->prepare("
    SELECT 
        u.id,
        u.name,
        u.role,
        COALESCE(SUM(wh.hours_worked), 0) as total_hours,
        COALESCE(COUNT(DISTINCT wh.date), 0) as days_worked,
        (
            SELECT status FROM work_status 
            WHERE user_id = u.id 
            ORDER BY created_at DESC 
            LIMIT 1
        ) as current_status,
        (
            SELECT COUNT(*) FROM tasks WHERE user_id = u.id AND status = 'pending'
        ) as pending_tasks,
        (
            SELECT COUNT(*) FROM tasks WHERE user_id = u.id AND status = 'in_progress'
        ) as active_tasks
    FROM users u
    LEFT JOIN work_hours wh ON u.id = wh.user_id AND DATE_FORMAT(wh.date, '%Y-%m') = ?
    WHERE u.id = ?
    GROUP BY u.id
");

$stmt->bind_param("si", $month, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$stats = $result->fetch_assoc();

// Check if weekly hours are under 40
$week_start = date('Y-m-d', strtotime('monday this week'));
$stmt = $conn->prepare("SELECT COALESCE(SUM(hours_worked), 0) as week_hours FROM work_hours WHERE user_id = ? AND date >= ?");
$stmt->bind_param("is", $user_id, $week_start);
$stmt->execute();
$week_result = $stmt->get_result();
$week_data = $week_result->fetch_assoc();
$stats['week_hours'] = $week_data['week_hours'];
$stats['hours_warning'] = $week_data['week_hours'] < 40 ? 'Óralejártás!' : null;

echo json_encode($stats);

$stmt->close();
$conn->close();
?>
