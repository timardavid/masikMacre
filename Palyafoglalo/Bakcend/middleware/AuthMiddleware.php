<?php
/**
 * Authentication Middleware
 * Protects routes that require authentication
 */

class AuthMiddleware {
    private $authService;
    
    public function __construct() {
        $this->authService = new AuthService();
    }
    
    /**
     * Check if user is authenticated
     */
    public function check($requiredRole = null) {
        // Get headers - try multiple methods for compatibility
        $headers = [];
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
        } else {
            // Fallback for servers that don't support getallheaders()
            foreach ($_SERVER as $key => $value) {
                if (strpos($key, 'HTTP_') === 0) {
                    $headerName = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                    $headers[$headerName] = $value;
                }
            }
        }
        
        // Try multiple header name variations (case-insensitive)
        $authHeader = null;
        foreach (['Authorization', 'authorization', 'AUTHORIZATION'] as $headerName) {
            if (isset($headers[$headerName])) {
                $authHeader = $headers[$headerName];
                break;
            }
        }
        
        // Also try HTTP_ prefixed version from $_SERVER (Apache converts headers to HTTP_* format)
        if (!$authHeader && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        }
        
        // Also try with Apache's REDIRECT_HTTP_AUTHORIZATION (when using RewriteRule)
        if (!$authHeader && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }
        
        // Try reading from apache_request_headers() if available (last resort)
        if (!$authHeader && function_exists('apache_request_headers')) {
            $rawHeaders = apache_request_headers();
            if ($rawHeaders && isset($rawHeaders['Authorization'])) {
                $authHeader = $rawHeaders['Authorization'];
            } elseif ($rawHeaders && isset($rawHeaders['authorization'])) {
                $authHeader = $rawHeaders['authorization'];
            }
        }
        
        if (!$authHeader || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            error_log('AuthMiddleware: No valid Authorization header found. Headers: ' . json_encode($headers));
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Authentication required'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        $token = $matches[1];
        
        // Debug logging
        error_log('AuthMiddleware: Token received, length: ' . strlen($token));
        error_log('AuthMiddleware: Token preview: ' . substr($token, 0, 30) . '...');
        
        $user = $this->authService->getUserFromToken($token);
        
        if (!$user) {
            error_log('AuthMiddleware: Invalid or expired token');
            error_log('AuthMiddleware: Token: ' . substr($token, 0, 50));
            
            // Try to decode to see what's wrong
            try {
                $parts = explode('.', $token);
                if (count($parts) === 3) {
                    $payload = json_decode(base64_decode($parts[1]), true);
                    if ($payload) {
                        error_log('Token payload: ' . json_encode($payload));
                        if (isset($payload['exp'])) {
                            $now = time();
                            $exp = $payload['exp'];
                            $expired = $exp < $now;
                            error_log("Token exp check: now=$now, exp=$exp, expired=" . ($expired ? 'YES' : 'NO'));
                            error_log("Time until expiry: " . ($exp - $now) . " seconds");
                        }
                    }
                }
            } catch (Exception $e) {
                error_log('Token decode error: ' . $e->getMessage());
            }
            
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Invalid or expired token'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        error_log('AuthMiddleware: User authenticated successfully: ' . ($user['email'] ?? 'unknown'));
        
        // Check role if required
        if ($requiredRole !== null) {
            $userModel = new UserModel();
            $fullUser = $userModel->findById($user['id']);
            $roleModel = new RoleModel();
            $role = $roleModel->findById($fullUser['role_id']);
            
            if (!$role || $role['name'] !== $requiredRole) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Insufficient permissions'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }
        
        return $user;
    }
}


