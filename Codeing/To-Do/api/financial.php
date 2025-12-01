<?php
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
$method = $_SERVER['REQUEST_METHOD'];
$conn = getDBConnection();

switch ($method) {
    case 'GET':
        getFinancialData();
        break;
}

function getFinancialData() {
    global $conn;
    
    // Get total users
    $result = $conn->query("SELECT COUNT(*) as count FROM users WHERE status = 'active'");
    $activeUsers = $result->fetch_assoc()['count'];
    
    // Get total revenue this month
    $result = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM revenue 
                           WHERE DATE_FORMAT(date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')");
    $monthlyRevenue = $result->fetch_assoc()['total'];
    
    // Get total revenue all time
    $result = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM revenue");
    $totalRevenue = $result->fetch_assoc()['total'];
    
    // Get total salary costs
    $result = $conn->query("SELECT COALESCE(SUM(salary), 0) as total FROM users WHERE status = 'active'");
    $totalSalary = $result->fetch_assoc()['total'];
    
    // Get total products
    $result = $conn->query("SELECT COUNT(*) as count FROM products WHERE status = 'active'");
    $totalProducts = $result->fetch_assoc()['count'];
    
    // Get this month profit
    $monthlyProfit = $monthlyRevenue - $totalSalary;
    
    // Get revenue by department (from tasks/users)
    $result = $conn->query("SELECT department, COUNT(*) as count FROM users WHERE status = 'active' GROUP BY department");
    $departmentData = [];
    while ($row = $result->fetch_assoc()) {
        $departmentData[] = $row;
    }
    
    // Get weekly revenue
    $result = $conn->query("
        SELECT DATE_FORMAT(date, '%W') as day_name, COALESCE(SUM(amount), 0) as amount
        FROM revenue 
        WHERE WEEK(date) = WEEK(NOW()) AND YEAR(date) = YEAR(NOW())
        GROUP BY DATE_FORMAT(date, '%W')
        ORDER BY date
    ");
    $weeklyRevenue = [];
    while ($row = $result->fetch_assoc()) {
        $weeklyRevenue[] = $row;
    }
    
    // Get monthly revenue last 6 months
    $result = $conn->query("
        SELECT DATE_FORMAT(date, '%Y-%m') as month, COALESCE(SUM(amount), 0) as amount
        FROM revenue 
        WHERE date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(date, '%Y-%m')
        ORDER BY month
    ");
    $monthlyTrend = [];
    while ($row = $result->fetch_assoc()) {
        $monthlyTrend[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'activeUsers' => $activeUsers,
        'monthlyRevenue' => $monthlyRevenue,
        'totalRevenue' => $totalRevenue,
        'totalSalary' => $totalSalary,
        'totalProducts' => $totalProducts,
        'monthlyProfit' => $monthlyProfit,
        'departmentData' => $departmentData,
        'weeklyRevenue' => $weeklyRevenue,
        'monthlyTrend' => $monthlyTrend
    ]);
}

$conn->close();
?>
