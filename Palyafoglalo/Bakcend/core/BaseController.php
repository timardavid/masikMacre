<?php
/**
 * Base Controller
 * Provides common functionality for all controllers
 */

class BaseController {
    /**
     * Send JSON response
     */
    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Send success response
     */
    protected function success($data = null, $message = null, $statusCode = 200) {
        $response = ['success' => true];
        if ($message) $response['message'] = $message;
        if ($data !== null) $response['data'] = $data;
        $this->jsonResponse($response, $statusCode);
    }
    
    /**
     * Send error response
     */
    protected function error($message, $errors = null, $statusCode = 400) {
        $response = [
            'success' => false,
            'message' => $message
        ];
        if ($errors) $response['errors'] = $errors;
        $this->jsonResponse($response, $statusCode);
    }
    
    /**
     * Get request body as JSON
     */
    protected function getRequestBody() {
        $json = file_get_contents('php://input');
        return json_decode($json, true);
    }
    
    /**
     * Get query parameters
     */
    protected function getQueryParams() {
        return $_GET;
    }
    
    /**
     * Validate required fields
     */
    protected function validateRequired($data, $requiredFields) {
        $errors = [];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[] = "Field '{$field}' is required";
            }
        }
        return $errors;
    }
    
    /**
     * Get current authenticated user from token
     */
    protected function getCurrentUser() {
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
        
        // Try reading from php://input if Authorization header was stripped (last resort)
        if (!$authHeader && isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            // Some servers strip Authorization header, try to get from request headers directly
            $rawHeaders = apache_request_headers();
            if ($rawHeaders && isset($rawHeaders['Authorization'])) {
                $authHeader = $rawHeaders['Authorization'];
            } elseif ($rawHeaders && isset($rawHeaders['authorization'])) {
                $authHeader = $rawHeaders['authorization'];
            }
        }
        
        if (!$authHeader || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            error_log('Auth failed: No valid Authorization header found. Headers: ' . json_encode($headers));
            return null;
        }
        
        $token = $matches[1];
        $authService = new AuthService();
        $user = $authService->getUserFromToken($token);
        
        if (!$user) {
            error_log('Auth failed: Invalid token');
        }
        
        return $user;
    }
}

