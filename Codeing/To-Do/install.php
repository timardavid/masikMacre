<?php
/**
 * Quick Installation Script
 * This creates the database and sets up initial data
 */

$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'root';
$db_name = 'company_dashboard';

echo "<h1>🏢 Vállalati Dashboard - Telepítés</h1>";

// Read and execute SQL file
$sql = file_get_contents('database.sql');

// Connect to MySQL
$conn = new mysqli($db_host, $db_user, $db_pass);

if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error);
}

echo "<p>✓ MySQL kapcsolat létrejött</p>";

// Execute SQL
$commands = explode(';', $sql);

foreach ($commands as $command) {
    $command = trim($command);
    if (!empty($command)) {
        if (!$conn->query($command)) {
            // Ignore if database already exists
            if (strpos($conn->error, 'already exists') === false) {
                echo "<p style='color:orange;'>⚠ " . $conn->error . "</p>";
            }
        }
    }
}

echo "<p>✓ Adatbázis létrehozva</p>";
echo "<p>✓ Példaadatok feltöltve</p>";

$conn->close();

echo "<h2>✓ Telepítés kész!</h2>";
echo "<p>Nyissa meg a <a href='index.html'>kezdőlapot</a></p>";
echo "<h3>Bejelentkezési adatok:</h3>";
echo "<ul>";
echo "<li>Admin: admin@company.com (jelszó: bármi)</li>";
echo "<li>IT: it@company.com</li>";
echo "<li>HR: hr@company.com</li>";
echo "<li>Pénzügy: finance@company.com</li>";
echo "<li>Ügyvezető: ceo@company.com</li>";
echo "</ul>";
?>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    h1 { color: #2563eb; }
    h2 { color: #10b981; }
    p { margin: 10px 0; }
    ul { margin-left: 20px; }
</style>
