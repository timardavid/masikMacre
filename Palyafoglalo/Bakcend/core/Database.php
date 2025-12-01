<?php
/**
 * Database Connection Singleton
 * Handles PDO connection to MySQL database
 */

class Database {
    private static $instance = null;
    private $connection;
    private $config;
    
    private function __construct() {
        $configFile = __DIR__ . '/../config/config.php';
        if (!file_exists($configFile)) {
            throw new Exception('Configuration file not found');
        }
        
        $this->config = require $configFile;
        $this->connect();
    }
    
    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Establish database connection
     */
    private function connect() {
        $db = $this->config['database'];
        
        // For MAMP, use 127.0.0.1 instead of localhost to force TCP/IP
        $host = ($db['host'] === 'localhost') ? '127.0.0.1' : $db['host'];
        
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $host,
            $db['port'],
            $db['database'],
            $db['charset']
        );
        
        try {
            $this->connection = new PDO(
                $dsn,
                $db['username'],
                $db['password'],
                $db['options']
            );
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            throw new Exception('Database connection failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Get PDO connection
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Prevent cloning
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

