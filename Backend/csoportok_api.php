<?php
// Return JSON of kindergarten groups with associated staff
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db_connection.php';

// Security headers are set in config.php
header('Content-Type: application/json; charset=utf-8');

// Allow only GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

try {
    // The actual schema uses `csoportok` (id, nev, photo_url, leiras, active, display_order)
    // and assigns staff members via `staff_member.csoport_id`.
    $sql = "
        SELECT 
            c.id AS group_id,
            c.nev AS group_name,
            c.photo_url AS group_photo_url,
            c.leiras AS group_description,
            sm.id AS staff_id,
            sm.name AS staff_name,
            sm.role_title AS staff_role_title,
            sc.name AS staff_category_name
        FROM csoportok c
        LEFT JOIN staff_member sm ON sm.csoport_id = c.id AND sm.active = 1
        LEFT JOIN staff_category sc ON sc.id = sm.category_id
        WHERE c.active = 1
        ORDER BY c.display_order ASC, c.id ASC, sc.display_order ASC, sm.sort_order ASC, sm.name ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $groupsById = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $groupId = (int)$row['group_id'];

        if (!isset($groupsById[$groupId])) {
            // Hardcoded child counts as provided by user
            $childCounts = [
                1 => 16, // Pillangó csoport
                2 => 26, // Százszorszép csoport  
                3 => 23  // Szivárvány csoport
            ];
            
            $groupsById[$groupId] = [
                'id' => $groupId,
                'nev' => $row['group_name'],
                'photo_url' => $row['group_photo_url'],
                'leiras' => $row['group_description'],
                'gyerekek_szama' => $childCounts[$groupId] ?? null,
                'munkatarsak' => []
            ];
        }

        if (!empty($row['staff_id'])) {
            $groupsById[$groupId]['munkatarsak'][] = [
                'id' => (int)$row['staff_id'],
                'nev' => $row['staff_name'],
                'beosztas' => $row['staff_role_title'],
                'kategoria' => $row['staff_category_name']
            ];
        }
    }

    $groups = array_values($groupsById);

    echo json_encode([
        'status' => 'ok',
        'count' => count($groups),
        'data' => $groups
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Hiba történt a csoportok lekérdezése során.'
        // 'details' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>



