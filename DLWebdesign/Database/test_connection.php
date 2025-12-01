<?php
/**
 * Adatbázis kapcsolat tesztelő script
 * Futtasd ezt a fájlt a böngészőben: http://localhost:8888/DLWebdesign/Database/test_connection.php
 */

require_once 'config.php';

echo "<!DOCTYPE html>
<html lang='hu'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Adatbázis Kapcsolat Teszt</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 10px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #28a745;
            margin: 20px 0;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #dc3545;
            margin: 20px 0;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #17a2b8;
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .check {
            color: #28a745;
            font-weight: bold;
        }
        .cross {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class='container'>";

echo "<h1>🔍 DLWebdesign - Adatbázis Kapcsolat Teszt</h1>";

try {
    $db = Database::getInstance()->getConnection();
    
    echo "<div class='success'>
            <strong>✅ Sikeres kapcsolat!</strong><br>
            Az adatbázis kapcsolat sikeresen létrejött.
          </div>";
    
    // Kapcsolat információk
    echo "<h2>📋 Kapcsolat Részletek</h2>";
    echo "<table>";
    echo "<tr><th>Paraméter</th><th>Érték</th></tr>";
    echo "<tr><td>Host</td><td>" . DB_HOST . "</td></tr>";
    echo "<tr><td>Port</td><td>" . DB_PORT . "</td></tr>";
    echo "<tr><td>Adatbázis</td><td>" . DB_NAME . "</td></tr>";
    echo "<tr><td>Felhasználó</td><td>" . DB_USER . "</td></tr>";
    echo "<tr><td>Karakterkódolás</td><td>" . DB_CHARSET . "</td></tr>";
    echo "<tr><td>Időzóna</td><td>" . TIMEZONE . "</td></tr>";
    echo "</table>";
    
    // Táblák ellenőrzése
    echo "<h2>🗃️ Adatbázis Táblák</h2>";
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "<div class='success'>
                <strong>Talált táblák száma: " . count($tables) . "</strong>
              </div>";
        
        echo "<table>";
        echo "<tr><th>#</th><th>Tábla neve</th><th>Rekordok száma</th></tr>";
        
        $i = 1;
        foreach ($tables as $table) {
            $countStmt = $db->query("SELECT COUNT(*) FROM `$table`");
            $count = $countStmt->fetchColumn();
            echo "<tr>";
            echo "<td>$i</td>";
            echo "<td><strong>$table</strong></td>";
            echo "<td>$count</td>";
            echo "</tr>";
            $i++;
        }
        echo "</table>";
        
        // Admin felhasználó ellenőrzés
        if (in_array('users', $tables)) {
            echo "<h2>👤 Admin Felhasználó</h2>";
            $adminStmt = $db->query("SELECT username, email, role, status FROM users WHERE role = 'admin' LIMIT 1");
            $admin = $adminStmt->fetch();
            
            if ($admin) {
                echo "<div class='success'>";
                echo "<strong>Admin fiók megtalálva:</strong><br>";
                echo "Felhasználónév: <strong>{$admin['username']}</strong><br>";
                echo "Email: <strong>{$admin['email']}</strong><br>";
                echo "Státusz: <strong>{$admin['status']}</strong>";
                echo "</div>";
                
                echo "<div class='info'>
                        <strong>ℹ️ Bejelentkezési adatok:</strong><br>
                        Felhasználónév: <code>admin</code><br>
                        Jelszó: <code>admin123</code><br>
                        <em>(Változtasd meg az első bejelentkezés után!)</em>
                      </div>";
            } else {
                echo "<div class='error'>Nem található admin felhasználó az adatbázisban!</div>";
            }
        }
        
        // Kategóriák ellenőrzése
        if (in_array('categories', $tables)) {
            echo "<h2>📁 Kategóriák</h2>";
            $catStmt = $db->query("SELECT name, slug, status FROM categories ORDER BY display_order");
            $categories = $catStmt->fetchAll();
            
            if (count($categories) > 0) {
                echo "<table>";
                echo "<tr><th>Név</th><th>Slug</th><th>Státusz</th></tr>";
                foreach ($categories as $cat) {
                    echo "<tr>";
                    echo "<td>{$cat['name']}</td>";
                    echo "<td><code>{$cat['slug']}</code></td>";
                    echo "<td>{$cat['status']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
        
    } else {
        echo "<div class='error'>
                <strong>⚠️ Figyelem!</strong><br>
                Az adatbázis létezik, de még nincsenek benne táblák.<br>
                Importáld be a <code>database_structure.sql</code> fájlt!
              </div>";
    }
    
    echo "<div class='success'>
            <strong>✅ Minden rendben!</strong><br>
            Az adatbázis sikeresen be van állítva és működik.
          </div>";
    
} catch (PDOException $e) {
    echo "<div class='error'>
            <strong>❌ Kapcsolat hiba!</strong><br>
            " . htmlspecialchars($e->getMessage()) . "
          </div>";
    
    echo "<h2>🔧 Lehetséges megoldások:</h2>";
    echo "<div class='info'>";
    echo "<ol>";
    echo "<li>Ellenőrizd, hogy a MAMP fut-e</li>";
    echo "<li>Ellenőrizd a port beállítást (Mac: 8889, Windows: 3306)</li>";
    echo "<li>Hozd létre a <code>dlwebdesign_db</code> adatbázist a phpMyAdmin-ban</li>";
    echo "<li>Importáld be a <code>database_structure.sql</code> fájlt</li>";
    echo "<li>Ellenőrizd a <code>config.php</code> fájlban a beállításokat</li>";
    echo "</ol>";
    echo "</div>";
}

echo "</div>
</body>
</html>";
?>

