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
        $year = $_GET['year'] ?? '2026';
        
        // Statikus adatok a beiratkozáshoz
        $enrollmentData = [
            'school_year' => $year,
            'notice' => 'Kedves Szülők! A beiratkozási időszak megkezdődött. Kérjük, hogy a megadott időpontokban jelentkezzenek beiratkozási szándékukkal.',
            'period_text' => '2026. január 15. - február 15.',
            'start_date' => '2026-01-15',
            'end_date' => '2026-02-15',
            'status' => 'folyamatban',
            'documents' => [
                'Személyi igazolvány (szülő)',
                'Lakcímkártya',
                'Gyermek születési anyakönyvi kivonata',
                'Egészségügyi igazolás',
                'Oltási igazolvány',
                '2 db fénykép (3x4 cm)',
                'Beiratkozási kérelem'
            ],
            'mandatory_condition' => 'A 3 éves gyermekek kötelezően beíratandók az óvodába a nevelési év kezdetét megelőzően.',
            'optional_condition' => 'A 2-3 éves gyermekek fakultatívan beírathatók, ha van hely az intézményben.',
            'signature_place_date' => 'Himesháza, 2026. január 10.',
            'signature_name' => 'Leib Rolandné',
            'signature_title' => 'Óvodavezető'
        ];
        
        echo json_encode([
            'success' => true,
            'data' => $enrollmentData
        ], JSON_UNESCAPED_UNICODE);
        
    } else {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Csak GET kérések támogatottak'
        ], JSON_UNESCAPED_UNICODE);
    }
    
} catch (Exception $e) {
    error_log("Enrollment API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Szerver hiba történt'
    ], JSON_UNESCAPED_UNICODE);
}
?>
