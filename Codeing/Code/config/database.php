<?php
/**
 * Database Configuration
 * Fitness Studio Dynamic Website
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'fitness_studio');
define('DB_USER', 'root');
define('DB_PASS', 'root'); // Default MAMP password
define('DB_CHARSET', 'utf8mb4');

// Site configuration
define('SITE_URL', 'http://localhost:8888/Codeing/Code/');
define('ADMIN_EMAIL', 'admin@fitnessstudio.com');

// Security settings
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_TIMEOUT', 3600); // 1 hour

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('America/New_York');

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Database connection class
 */
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
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
}

/**
 * Utility functions
 */
class Utils {
    
    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }
    
    /**
     * Validate CSRF token
     */
    public static function validateCSRFToken($token) {
        return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }
    
    /**
     * Sanitize input data
     */
    public static function sanitizeInput($data) {
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validate email
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    /**
     * Send JSON response
     */
    public static function sendJSONResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Log error
     */
    public static function logError($message, $file = 'error.log') {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message" . PHP_EOL;
        file_put_contents($file, $logMessage, FILE_APPEND | LOCK_EX);
    }
}

/**
 * API Response class
 */
class APIResponse {
    private $data;
    private $status;
    private $message;
    
    public function __construct($data = null, $status = 200, $message = '') {
        $this->data = $data;
        $this->status = $status;
        $this->message = $message;
    }
    
    public function setData($data) {
        $this->data = $data;
        return $this;
    }
    
    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }
    
    public function setMessage($message) {
        $this->message = $message;
        return $this;
    }
    
    public function send() {
        $response = [
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data,
            'timestamp' => date('c')
        ];
        
        Utils::sendJSONResponse($response, $this->status);
    }
}

// Initialize database connection
try {
    $db = Database::getInstance()->getConnection();
} catch (Exception $e) {
    Utils::logError("Database initialization failed: " . $e->getMessage());
    Utils::sendJSONResponse(['error' => 'Database connection failed'], 500);
}
?>
