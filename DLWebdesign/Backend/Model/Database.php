<?php
/**
 * Database Connection Singleton
 * JDW
 */

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        require_once __DIR__ . '/../../Database/config.php';
        $this->connection = \Database::getInstance()->getConnection();
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
    
    // Prevent cloning
    private function __clone() {}
    
    // Prevent unserialize
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

