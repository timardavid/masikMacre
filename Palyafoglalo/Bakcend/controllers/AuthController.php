<?php
/**
 * Auth Controller
 * Handles authentication endpoints
 */

class AuthController extends BaseController {
    private $authService;
    
    public function __construct() {
        $this->authService = new AuthService();
    }
    
    /**
     * POST /api/v1/auth/login
     */
    public function login() {
        $data = $this->getRequestBody();
        
        $errors = $this->validateRequired($data, ['email', 'password']);
        if (!empty($errors)) {
            $this->error('Validation failed', $errors, 400);
        }
        
        $result = $this->authService->authenticate($data['email'], $data['password']);
        
        if ($result['success']) {
            $this->success($result, 'Login successful');
        } else {
            $this->error($result['message'], null, 401);
        }
    }
    
    /**
     * GET /api/v1/auth/me
     */
    public function me() {
        $token = $this->getAuthToken();
        
        if (!$token) {
            $this->error('Unauthorized', null, 401);
        }
        
        $user = $this->authService->getUserFromToken($token);
        
        if (!$user) {
            $this->error('Invalid token', null, 401);
        }
        
        $this->success($user);
    }
    
    /**
     * POST /api/v1/auth/register
     */
    public function register() {
        $data = $this->getRequestBody();
        
        $errors = $this->validateRequired($data, [
            'email', 'password', 'password_confirm', 'full_name'
        ]);
        
        if (!empty($errors)) {
            $this->error('Validation failed', $errors, 400);
        }
        
        // Email validation
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email format', ['email' => 'Please provide a valid email address'], 400);
        }
        
        // Password strength
        if (strlen($data['password']) < 8) {
            $this->error('Password too short', ['password' => 'Password must be at least 8 characters'], 400);
        }
        
        $result = $this->authService->register($data);
        
        if ($result['success']) {
            $this->success($result, 'Registration successful', 201);
        } else {
            $this->error($result['message'], null, 400);
        }
    }
    
    /**
     * POST /api/v1/auth/forgot-password
     */
    public function forgotPassword() {
        $data = $this->getRequestBody();
        
        $errors = $this->validateRequired($data, ['email']);
        if (!empty($errors)) {
            $this->error('Validation failed', $errors, 400);
        }
        
        $result = $this->authService->requestPasswordReset($data['email']);
        $this->success(null, $result['message']);
    }
    
    /**
     * POST /api/v1/auth/reset-password
     */
    public function resetPassword() {
        $data = $this->getRequestBody();
        
        $errors = $this->validateRequired($data, ['email', 'code', 'password']);
        if (!empty($errors)) {
            $this->error('Validation failed', $errors, 400);
        }
        
        // Password strength
        if (strlen($data['password']) < 8) {
            $this->error('Password too short', ['password' => 'Password must be at least 8 characters'], 400);
        }
        
        $result = $this->authService->resetPassword(
            $data['email'],
            $data['code'],
            $data['password']
        );
        
        if ($result['success']) {
            $this->success(null, $result['message']);
        } else {
            $this->error($result['message'], null, 400);
        }
    }
    
    /**
     * Get auth token from header
     */
    private function getAuthToken() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        
        if (!$authHeader || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return null;
        }
        
        return $matches[1];
    }
}

