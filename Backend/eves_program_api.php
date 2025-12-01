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

// Rate limiting
$clientIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
checkRateLimit($clientIP);

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $grouped = isset($_GET['grouped']) && $_GET['grouped'] == '1';
        
        if ($grouped) {
            // Csoportosított adatok hónapok szerint - 2025/2026 nevelési év
            $programData = [
                'school_year' => '2025/2026',
                'Szeptember' => [
                    ['label' => 'Szeptember 1.:', 'text' => 'Óvoda kezdés'],
                    ['label' => 'Szeptember 4.:', 'text' => 'Szülői értekezlet'],
                    ['label' => 'Szeptember:', 'text' => 'Új óvodások beszoktatása (folyamatos)'],
                    ['label' => 'Szeptember:', 'text' => 'Logopédiai felmérések'],
                    ['label' => 'Szeptember 30.:', 'text' => 'A népmese napja']
                ],
                'Október' => [
                    ['label' => 'Október 3.:', 'text' => 'Állatok világnapja (október 4.)'],
                    ['label' => 'Október 8.:', 'text' => 'Őszi hátizsákos kirándulás - Székelyszabar tanpálya'],
                    ['label' => 'Október 18.:', 'text' => 'Munkanap szombat október 24 ledolgozása'],
                    ['label' => 'Október 22.:', 'text' => 'Megemlékezés Október 23.-ról'],
                    ['label' => 'Október 23.:', 'text' => 'Nemzeti Ünnep - munkaszüneti nap (csütörtök)'],
                    ['label' => 'Október 24.:', 'text' => 'Munkaszüneti nap (péntek)'],
                    ['label' => 'Október 27.:', 'text' => 'Nevelés nélküli munkanap (hétfő)'],
                    ['label' => 'Október:', 'text' => 'Az iskolai őszi szünet október 31-ig lesz.']
                ],
                'November' => [
                    ['label' => 'November 1.:', 'text' => 'Mindenszentek (szombat)'],
                    ['label' => 'November 3.:', 'text' => 'Megemlékezés a Halottak napjáról, séta a temetőbe - gyertyagyújtás (hétfő)'],
                    ['label' => 'November 11.:', 'text' => '"Tök jó nap" - Márton-napi lampionos felvonulás (kedd)'],
                    ['label' => 'November 28.:', 'text' => 'Advent, karácsonyi készülődés hónapja - közös készülődés a szülőkkel csoportszinten (péntek)']
                ],
                'December' => [
                    ['label' => 'December 5.:', 'text' => 'Mikulásünnepség az óvodában (péntek)'],
                    ['label' => 'December 22-23:', 'text' => 'Óvodánk 13 óráig tart ügyeletet.'],
                    ['label' => 'December 24-28:', 'text' => 'Karácsony'],
                    ['label' => 'December 29-31:', 'text' => 'Nevelés nélküli munkanapok - téli karbantartás']
                ],
                'Január' => [
                    ['label' => 'Január 1-2.:', 'text' => 'Munkaszüneti napok'],
                    ['label' => 'Január 15.:', 'text' => 'Csoportszülői értekezletek']
                ],
                'Február' => [
                    ['label' => 'Február 2.:', 'text' => 'Medve nap'],
                    ['label' => 'Február 6.:', 'text' => 'Farsangi bál'],
                    ['label' => 'Február:', 'text' => 'Fánknap'],
                    ['label' => 'Február:', 'text' => 'Busók érkezése']
                ],
                'Március' => [
                    ['label' => 'Március 3-7.:', 'text' => 'IKT projekthét'],
                    ['label' => 'Március:', 'text' => 'Nyílt nap a leendő első osztályosok számára'],
                    ['label' => 'Március:', 'text' => 'Szülői értekezlet a leendő első osztályosok szüleinek'],
                    ['label' => 'Március 13.:', 'text' => 'Március 15-i ünnepség, koszorúzás (péntek)'],
                    ['label' => 'Március 23.:', 'text' => 'Víz világnapja'],
                    ['label' => 'Március 30.:', 'text' => 'Vár-Lak nyílt nap az óvodában (hétfő)']
                ],
                'Április' => [
                    ['label' => 'Április 3.:', 'text' => 'Nagypéntek (munkaszüneti nap)'],
                    ['label' => 'Április 6.:', 'text' => 'Húsvét hétfő (munkaszüneti nap)'],
                    ['label' => 'Április 7.:', 'text' => 'Nevelés nélküli munkanap (kedd)'],
                    ['label' => 'Április:', 'text' => 'Az iskolában még egész héten április 10-ig szünet lesz.'],
                    ['label' => 'Április 13.:', 'text' => 'A költészet napja (április 11)'],
                    ['label' => 'Április 20-24.:', 'text' => 'Német Nemzetiségi Projekthét - ez lehet hosszabb is'],
                    ['label' => 'Április:', 'text' => 'Óvodai beiratás'],
                    ['label' => 'Április 24.:', 'text' => 'Föld napja (péntek)']
                ],
                'Május' => [
                    ['label' => 'Május:', 'text' => 'Kirándulások csoportszinten - elmenős!!!!'],
                    ['label' => 'Május 1.:', 'text' => 'Munka ünnepe - munkaszüneti nap (péntek)'],
                    ['label' => 'Május 5.:', 'text' => 'Anyák napja csoportszinten 16 órától'],
                    ['label' => 'Május 8.:', 'text' => 'Madarak és Fák napja'],
                    ['label' => 'Május 21.:', 'text' => 'Méhek világnapja']
                ],
                'Június' => [
                    ['label' => 'Június:', 'text' => 'Sportnap - ovitorna torna, versenyek, meghívni az OVIKÉZI által, a BOZSIK ÁLTAL a vezetőket'],
                    ['label' => 'Június 4.:', 'text' => 'Nemzeti összetartozás napja'],
                    ['label' => 'Június 12.:', 'text' => 'Ballagás és évzáró (péntek)']
                ],
                'Nyári szünet!' => [
                    ['label' => '2026. augusztus 3. - augusztus 28.:', 'text' => 'Nyári szünet']
                ]
            ];
        } else {
            // Egyszerű lista formátum
            $programData = [
                ['id' => 1, 'title' => 'Nevelési év kezdete', 'month' => 9, 'starts_on' => '2025-09-01'],
                ['id' => 2, 'title' => 'Szülői értekezlet', 'month' => 9, 'starts_on' => '2025-09-15'],
                ['id' => 3, 'title' => 'Mikulás nap', 'month' => 12, 'starts_on' => '2025-12-06'],
                ['id' => 4, 'title' => 'Karácsonyi ünnepség', 'month' => 12, 'starts_on' => '2025-12-20'],
                ['id' => 5, 'title' => 'Beiratkozási időszak', 'month' => 1, 'starts_on' => '2026-01-15', 'ends_on' => '2026-01-31'],
                ['id' => 6, 'title' => 'Tavaszi ünnepség', 'month' => 3, 'starts_on' => '2026-03-25'],
                ['id' => 7, 'title' => 'Gyermeknap', 'month' => 6, 'starts_on' => '2026-06-01'],
                ['id' => 8, 'title' => 'Nevelési év zárása', 'month' => 6, 'starts_on' => '2026-06-15']
            ];
        }
        
        echo json_encode($programData, JSON_UNESCAPED_UNICODE);
        
    } else {
        http_response_code(405);
        echo json_encode([
            'error' => 'Csak GET kérések támogatottak'
        ], JSON_UNESCAPED_UNICODE);
    }
    
} catch (Exception $e) {
    error_log("Eves Program API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Szerver hiba történt'
    ], JSON_UNESCAPED_UNICODE);
}
?>
