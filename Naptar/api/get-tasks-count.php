<?php
require_once '../config.php';

// Teendők száma dátum szerint (naptárhoz)
$conn = getDBConnection();

$month = $_GET['month'] ?? null; // YYYY-MM formátum

if ($month) {
    $stmt = $conn->prepare("SELECT task_date, COUNT(*) as count FROM tasks WHERE DATE_FORMAT(task_date, '%Y-%m') = ? GROUP BY task_date");
    $stmt->bind_param("s", $month);
} else {
    $stmt = $conn->prepare("SELECT task_date, COUNT(*) as count FROM tasks GROUP BY task_date");
}

$stmt->execute();
$result = $stmt->get_result();

$counts = [];
while ($row = $result->fetch_assoc()) {
    $counts[$row['task_date']] = (int)$row['count'];
}

$stmt->close();
$conn->close();

sendJSON(['counts' => $counts]);


