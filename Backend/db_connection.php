<?php
require_once __DIR__ . '/config.php';

// Rate limiting
checkRateLimit($_SERVER['REMOTE_ADDR'] ?? 'unknown');

$dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE              => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE   => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES     => false,
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false, // SSL for production
    PDO::ATTR_PERSISTENT           => false, // No persistent connections for security
];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);

} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json'); // Itt is beállítjuk, ha még nem volt

    // Küldj vissza JSON hibaüzenetet, hogy a frontend is fel tudja dolgozni
    echo json_encode([
        "status" => "error",
        "message" => "Hiba történt az adatbázis kapcsolódás során. Kérem, próbálja újra később.",
        // A részletes hibaüzenetet csak fejlesztéshez érdemes kiírni:
        // "details" => $e->getMessage()
    ]);

    // Éles környezetben a részletes hibát naplózni kellene egy log fájlba:
    error_log("Adatbázis kapcsolódási hiba: " . $e->getMessage() . " a " . $_SERVER['REQUEST_URI'] . " oldalon.");

    // Leállítja a szkript további futását
    exit();
}

?>