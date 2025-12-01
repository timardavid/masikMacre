<?php
require_once 'config.php';

// Set security headers
if (function_exists('setSecurityHeaders')) {
    setSecurityHeaders();
}

// CORS headers
if (function_exists('setCorsHeaders')) {
    setCorsHeaders();
}
header('Content-Type: application/json; charset=utf-8');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        require_once 'db_connection.php';
        
        // 1. Lejárt programok törlése (ahol a starts_on dátum már elmúlt)
        $deleteExpiredSql = "DELETE FROM annual_program_items WHERE starts_on IS NOT NULL AND starts_on < CURDATE()";
        $pdo->exec($deleteExpiredSql);
        
        // 2. Következő 4 program lekérdezése az annual_program_items táblából
        // Csak azokat válasszuk ki, ahol van starts_on dátum és az jövőbeli
        $newsSql = "
            SELECT 
                id,
                title as cim,
                IFNULL(details, '') as szoveg,
                starts_on as datum
            FROM annual_program_items 
            WHERE starts_on IS NOT NULL 
            AND starts_on >= CURDATE() 
            ORDER BY starts_on ASC 
            LIMIT 4
        ";
        
        $stmt = $pdo->prepare($newsSql);
        $stmt->execute();
        $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'status' => 'success',
            'data' => $news
        ], JSON_UNESCAPED_UNICODE);
        
    } else {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Nem támogatott metódus'], JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>