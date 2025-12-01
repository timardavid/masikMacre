<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'company_dashboard');

// Create connection with error handling
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        // Return a better error for debugging
        header('Content-Type: application/json; charset=utf-8');
        die(json_encode([
            'error' => 'Database connection failed',
            'message' => $conn->connect_error,
            'host' => DB_HOST,
            'database' => DB_NAME
        ]));
    }
    
    $conn->set_charset("utf8");
    return $conn;
}

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
