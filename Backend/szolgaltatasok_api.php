<?php
// Return JSON of services
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
    // Fetch services ordered by sort_order
    $sql = "
        SELECT 
            id,
            nev,
            leiras,
            idopont,
            kep_url,
            szemelyek,
            aktualis,
            sorrend
        FROM szolgaltatasok
        WHERE aktualis = 1
        ORDER BY sorrend ASC, id ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $services = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $services[] = [
            'id' => (int)$row['id'],
            'nev' => $row['nev'],
            'leiras' => $row['leiras'],
            'idopont' => $row['idopont'],
            'kep_url' => $row['kep_url'],
            'szemelyek' => $row['szemelyek'],
            'sorrend' => (int)$row['sorrend']
        ];
    }

    echo json_encode([
        'status' => 'ok',
        'count' => count($services),
        'data' => $services
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Hiba történt a szolgáltatások lekérdezése során.'
        // 'details' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
