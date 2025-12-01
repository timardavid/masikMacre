<?php
// Production security configuration

// Environment detection
$isProduction = ($_SERVER['HTTP_HOST'] ?? '') !== 'localhost' && 
                ($_SERVER['HTTP_HOST'] ?? '') !== '127.0.0.1' &&
                !str_contains($_SERVER['HTTP_HOST'] ?? '', '.local');

// Database configuration - PRODUCTION SETTINGS
$dbHost = $isProduction ? ($_ENV['DB_HOST'] ?? 'localhost') : 'localhost';
$dbName = $isProduction ? ($_ENV['DB_NAME'] ?? 'himeshazi_ovoda') : 'himeshazi_ovoda';
$dbUser = $isProduction ? ($_ENV['DB_USER'] ?? 'ovoda_user') : 'root';
$dbPass = $isProduction ? ($_ENV['DB_PASS'] ?? '') : 'root';

// Security headers function
function setSecurityHeaders() {
    if (!headers_sent()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Content-Security-Policy: default-src \'self\'; img-src \'self\' data: https:; style-src \'self\' \'unsafe-inline\' https://fonts.googleapis.com https://cdnjs.cloudflare.com; font-src \'self\' https://fonts.gstatic.com https://cdnjs.cloudflare.com; script-src \'self\' \'unsafe-inline\';');
        
        // HSTS for production
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        }
    }
}

// CORS configuration function
function setCorsHeaders() {
    global $isProduction;
    
    if (!headers_sent()) {
        if ($isProduction) {
            // Production: specific domains only
            $allowedOrigins = [
                'https://yourdomain.com',
                'https://www.yourdomain.com'
            ];
            
            $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
            if (in_array($origin, $allowedOrigins)) {
                header("Access-Control-Allow-Origin: $origin");
            }
        } else {
            // Development: allow all
            header("Access-Control-Allow-Origin: *");
        }
        
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Max-Age: 86400');
    }
}

// Handle preflight requests
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    setCorsHeaders();
    http_response_code(200);
    exit();
}

// Rate limiting (ACTIVE in production)
function checkRateLimit($ip, $limit = 100, $window = 3600) {
    global $isProduction;
    
    if (!$isProduction) {
        return true; // Disabled in development
    }
    
    $file = sys_get_temp_dir() . '/rate_limit_' . md5($ip);
    $current = time();
    
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if ($current - $data['time'] < $window) {
            if ($data['count'] >= $limit) {
                http_response_code(429);
                echo json_encode(['error' => 'Too many requests']);
                exit();
            }
            $data['count']++;
        } else {
            $data = ['time' => $current, 'count' => 1];
        }
    } else {
        $data = ['time' => $current, 'count' => 1];
    }
    
    file_put_contents($file, json_encode($data));
    return true;
}

// Input validation and sanitization
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// CSRF protection
function generateCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    return $token;
}

function validateCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Error logging
function logError($message, $context = []) {
    $logEntry = date('Y-m-d H:i:s') . ' - ' . $message;
    if (!empty($context)) {
        $logEntry .= ' - Context: ' . json_encode($context);
    }
    error_log($logEntry . PHP_EOL, 3, __DIR__ . '/../logs/error.log');
}

// Session security function
function setSessionSecurity() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_samesite', 'Strict');
    }
}

// Production error settings
if ($isProduction) {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/php_errors.log');
} else {
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
}

// Log file path
define('LOG_FILE', __DIR__ . '/../logs/error.log');
?>
