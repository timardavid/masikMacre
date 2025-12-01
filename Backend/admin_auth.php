<?php
require_once __DIR__ . '/config.php';

class AdminAuth {
    private $pdo;
    private $sessionTimeout = 3600; // 1 hour
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        session_start();
    }
    
    public function login($username, $password) {
        // Rate limiting for login attempts
        $this->checkLoginRateLimit();
        
        $stmt = $this->pdo->prepare("
            SELECT id, username, password_hash, last_login, failed_attempts 
            FROM admin_users 
            WHERE username = ? AND active = 1
        ");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if (!$user) {
            $this->logFailedAttempt($username);
            return false;
        }
        
        // Check if account is locked
        if ($user['failed_attempts'] >= 5) {
            $this->logFailedAttempt($username);
            return false;
        }
        
        // Verify password
        if (password_verify($password, $user['password_hash'])) {
            // Reset failed attempts
            $this->resetFailedAttempts($user['id']);
            
            // Update last login
            $this->updateLastLogin($user['id']);
            
            // Set session
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_login_time'] = time();
            $_SESSION['csrf_token'] = generateCSRFToken();
            
            return true;
        }
        
        $this->logFailedAttempt($username, $user['id']);
        return false;
    }
    
    public function isLoggedIn() {
        if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_login_time'])) {
            return false;
        }
        
        // Check session timeout
        if (time() - $_SESSION['admin_login_time'] > $this->sessionTimeout) {
            $this->logout();
            return false;
        }
        
        // Refresh session time
        $_SESSION['admin_login_time'] = time();
        
        return true;
    }
    
    public function logout() {
        session_destroy();
        session_start();
    }
    
    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'Authentication required']);
            exit();
        }
    }
    
    private function checkLoginRateLimit() {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        checkRateLimit($ip, 5, 300); // 5 attempts per 5 minutes
    }
    
    private function logFailedAttempt($username, $userId = null) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        logError("Failed login attempt", [
            'username' => $username,
            'ip' => $ip,
            'user_agent' => $userAgent
        ]);
        
        if ($userId) {
            $stmt = $this->pdo->prepare("
                UPDATE admin_users 
                SET failed_attempts = failed_attempts + 1, 
                    last_failed_attempt = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$userId]);
        }
    }
    
    private function resetFailedAttempts($userId) {
        $stmt = $this->pdo->prepare("
            UPDATE admin_users 
            SET failed_attempts = 0, last_failed_attempt = NULL 
            WHERE id = ?
        ");
        $stmt->execute([$userId]);
    }
    
    private function updateLastLogin($userId) {
        $stmt = $this->pdo->prepare("
            UPDATE admin_users 
            SET last_login = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$userId]);
    }
}
?>
