<?php
require_once 'config.php';
require_once 'db_connection.php';

try {
    // Töröljük az összes hírt
    $sql = "DELETE FROM hirek";
    $pdo->exec($sql);
    
    echo "Összes hír törölve. Frissítsd az oldalt, hogy újak generálódjanak!";
    
} catch (Exception $e) {
    echo "Hiba: " . $e->getMessage();
}
?>

