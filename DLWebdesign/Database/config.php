<?php
/**
 * Adatbázis kapcsolat konfiguráció
 * JDW
 */

// MAMP alapértelmezett beállítások
define('DB_HOST', 'localhost');
define('DB_PORT', '8889'); // MAMP alapértelmezett MySQL port (Windows: 3306, Mac: 8889)
define('DB_NAME', 'jdw_db');
define('DB_USER', 'root');
define('DB_PASS', 'root'); // MAMP alapértelmezett jelszó

// Karakterkódolás
define('DB_CHARSET', 'utf8mb4');

// Időzóna beállítás
define('TIMEZONE', 'Europe/Budapest');
date_default_timezone_set(TIMEZONE);

// Hiba jelentés (fejlesztés során)
// Éles környezetben állítsd át: error_reporting(0); ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Adatbázis kapcsolat osztály
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Adatbázis kapcsolat hiba: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Singleton - klónozás tiltása
    private function __clone() {}
    
    // Singleton - unserialize tiltása
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

// Használat:
// $db = Database::getInstance()->getConnection();
?>

