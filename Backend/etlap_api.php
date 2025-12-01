<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db_connection.php';

setSecurityHeaders();
setCorsHeaders();
header('Content-Type: application/json; charset=utf-8');

try {
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    
    if ($method === 'GET') {
        // Először próbáljuk az aktuális hetet, majd a legfrissebb aktív étlapot
        $date = $_GET['date'] ?? date('Y-m-d');
        $dateObj = new DateTime($date);
        
        // Keressük meg azt a hetet, amibe ez a dátum esik
        $startOfWeek = clone $dateObj;
        $startOfWeek->modify('monday this week');
        $endOfWeek = clone $startOfWeek;
        $endOfWeek->modify('+4 days'); // Péntekig
        
        // Először próbáljuk az aktuális hetet
        $sql = "
            SELECT mw.id, mw.start_date, mw.end_date, mw.group_type, mw.location
            FROM menu_weeks mw
            WHERE mw.start_date <= :end_date 
            AND mw.end_date >= :start_date
            AND mw.active = 1
            ORDER BY mw.start_date DESC
            LIMIT 1
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':start_date' => $startOfWeek->format('Y-m-d'),
            ':end_date' => $endOfWeek->format('Y-m-d')
        ]);
        
        $week = $stmt->fetch();
        
        // Ha nincs aktuális hétre, keressük a legfrissebb aktív étlapot
        if (!$week) {
            $fallbackSql = "
                SELECT mw.id, mw.start_date, mw.end_date, mw.group_type, mw.location
                FROM menu_weeks mw
                WHERE mw.active = 1
                ORDER BY mw.start_date DESC
                LIMIT 1
            ";
            
            $fallbackStmt = $pdo->prepare($fallbackSql);
            $fallbackStmt->execute();
            $week = $fallbackStmt->fetch();
        }
        
        if (!$week) {
            echo json_encode([
                'success' => false,
                'message' => 'Nincs elérhető étlap.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        // Napok lekérdezése
        $daysSql = "
            SELECT 
                day_name, day_date, day_order,
                tizorai_items, tizorai_allergens, tizorai_nutrition,
                ebed_items, ebed_allergens, ebed_nutrition,
                uzsonna_items, uzsonna_allergens, uzsonna_nutrition,
                total_nutrition
            FROM menu_days
            WHERE menu_week_id = :week_id
            ORDER BY day_order ASC
        ";
        
        $daysStmt = $pdo->prepare($daysSql);
        $daysStmt->execute([':week_id' => $week['id']]);
        $days = $daysStmt->fetchAll();
        
        // Parse JSON adatok
        foreach ($days as &$day) {
            $day['tizorai_items'] = json_decode($day['tizorai_items'], true) ?? [];
            $day['tizorai_nutrition'] = json_decode($day['tizorai_nutrition'], true) ?? [];
            $day['ebed_items'] = json_decode($day['ebed_items'], true) ?? [];
            $day['ebed_nutrition'] = json_decode($day['ebed_nutrition'], true) ?? [];
            $day['uzsonna_items'] = json_decode($day['uzsonna_items'], true) ?? [];
            $day['uzsonna_nutrition'] = json_decode($day['uzsonna_nutrition'], true) ?? [];
            $day['total_nutrition'] = json_decode($day['total_nutrition'], true) ?? [];
        }
        
        echo json_encode([
            'success' => true,
            'week' => $week,
            'days' => $days
        ], JSON_UNESCAPED_UNICODE);
        
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    error_log("Etlap API hiba: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Hiba történt az adatok lekérdezése során.'
    ], JSON_UNESCAPED_UNICODE);
}

