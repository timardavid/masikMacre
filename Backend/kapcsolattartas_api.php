<?php
// Return JSON of contact information with staff and groups
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/db_connection.php';

// Allow only GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

try {
    // Fetch contact information with staff and group details
    $sql = "
        SELECT 
            k.kapcsolattartas_id,
            k.tipus,
            k.esemeny_nev,
            k.kezdet_ido,
            k.veg_ido,
            k.gyakorisag,
            k.nap_a_heten,
            k.megjegyzes,
            sm.name AS staff_name,
            sm.role_title AS staff_role,
            c.nev AS group_name
        FROM kapcsolattartas k
        LEFT JOIN staff_member sm ON sm.name = SUBSTRING_INDEX(SUBSTRING_INDEX(k.esemeny_nev, ' - ', -1), ' (', 1)
        LEFT JOIN csoportok c ON (
            (k.esemeny_nev LIKE CONCAT('%', c.nev, '%')) OR
            (k.esemeny_nev LIKE '%Pillangó%' AND c.nev = 'Pillangó csoport') OR
            (k.esemeny_nev LIKE '%Százszorszép%' AND c.nev = 'Százszorszép csoport') OR
            (k.esemeny_nev LIKE '%Szivárvány%' AND c.nev = 'Szivárvány csoport')
        )
        WHERE (k.tipus = 'Szülői értekezlet') OR (k.tipus = 'Fogadó óra' AND (sm.role_title IS NULL OR sm.role_title != 'Dajka'))
        ORDER BY k.tipus ASC, k.kapcsolattartas_id ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $contactData = [
        'szuloi_ertekezlet' => [],
        'fogado_ora' => [
            'igazgato' => [],
            'csoportok' => []
        ]
    ];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['tipus'] === 'Szülői értekezlet') {
            $contactData['szuloi_ertekezlet'][] = [
                'id' => (int)$row['kapcsolattartas_id'],
                'esemeny_nev' => $row['esemeny_nev'],
                'gyakorisag' => $row['gyakorisag'],
                'nap_a_heten' => $row['nap_a_heten'],
                'megjegyzes' => $row['megjegyzes']
            ];
        } elseif ($row['tipus'] === 'Fogadó óra') {
            $contactInfo = [
                'id' => (int)$row['kapcsolattartas_id'],
                'esemeny_nev' => $row['esemeny_nev'],
                'kezdet_ido' => $row['kezdet_ido'],
                'veg_ido' => $row['veg_ido'],
                'gyakorisag' => $row['gyakorisag'],
                'nap_a_heten' => $row['nap_a_heten'],
                'megjegyzes' => $row['megjegyzes'],
                'staff_name' => $row['staff_name'],
                'staff_role' => $row['staff_role'],
                'group_name' => $row['group_name']
            ];

            // Determine if it's director or group contact
            if (strpos($row['esemeny_nev'], 'Igazgató') !== false) {
                $contactData['fogado_ora']['igazgato'][] = $contactInfo;
            } else {
                $contactData['fogado_ora']['csoportok'][] = $contactInfo;
            }
        }
    }

    echo json_encode([
        'status' => 'ok',
        'data' => $contactData
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Hiba történt a kapcsolattartási információk lekérdezése során.'
        // 'details' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
