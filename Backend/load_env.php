<?php
/**
 * .env fájl betöltése környezeti változókká
 * Egyszerű megoldás .env fájlok kezelésére
 */

function loadEnvFile($envPath = null) {
    if ($envPath === null) {
        $envPath = __DIR__ . '/.env';
    }
    
    if (!file_exists($envPath)) {
        return; // .env fájl nincs, használjuk a szerver környezeti változóit
    }
    
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Kihagyja a kommenteket
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Feldolgozza a kulcs=érték párokat
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Eltávolítja az idézőjeleket ha vannak
            if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                $value = substr($value, 1, -1);
            }
            
            // Csak akkor állítja be, ha még nincs beállítva környezeti változóként
            if (!isset($_ENV[$key]) && !isset($_SERVER[$key])) {
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

// Automatikusan betölti a .env fájlt
loadEnvFile();

