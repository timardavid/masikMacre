<?php
/**
 * Authentication Service
 * Handles user authentication and authorization
 */

class AuthService {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
    }
    
    /**
     * Authenticate user with email and password
     */
    public function authenticate($email, $password) {
        // Validate email format first
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Az email cím formátuma nem megfelelő'];
        }
        
        $user = $this->userModel->findByEmail($email);
        
        if (!$user) {
            return ['success' => false, 'message' => 'A megadott email cím nem regisztrált az oldalon'];
        }
        
        if (!$user['is_active']) {
            return ['success' => false, 'message' => 'A fiók inaktív'];
        }
        
        if (!$this->userModel->verifyPassword($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Hibás jelszó'];
        }
        
        // Get role_name using database connection
        $db = $this->userModel->getDb();
        $stmt = $db->prepare(
            "SELECT r.name as role_name 
             FROM roles r 
             WHERE r.id = ?"
        );
        $stmt->execute([$user['role_id']]);
        $role = $stmt->fetch();
        if ($role) {
            $user['role_name'] = $role['role_name'];
        }
        
        // Remove sensitive data
        unset($user['password_hash']);
        
        // Generate JWT token
        $token = $this->generateToken($user);
        
        return [
            'success' => true,
            'user' => $user,
            'token' => $token
        ];
    }
    
    /**
     * Generate JWT token (simplified version - use firebase/php-jwt in production)
     */
    private function generateToken($user) {
        $header = base64_encode(json_encode(['typ' => 'JWT', 'alg' => JWT_ALGORITHM]));
        $payload = base64_encode(json_encode([
            'user_id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role_id'],
            'exp' => time() + JWT_EXPIRATION
        ]));
        // Use raw header.payload string (before base64) for signature
        $signature = base64_encode(hash_hmac('sha256', "$header.$payload", JWT_SECRET, true));
        
        return "$header.$payload.$signature";
    }
    
    /**
     * Verify JWT token
     */
    public function verifyToken($token) {
        try {
            $parts = explode('.', $token);
            if (count($parts) !== 3) {
                error_log('AuthService: Invalid token format, parts: ' . count($parts));
                return null;
            }
            
            [$header, $payload, $signature] = $parts;
            
            // Calculate expected signature (same as generation: hash header.payload string)
            $expectedSignature = base64_encode(hash_hmac('sha256', "$header.$payload", JWT_SECRET, true));
            
            // Compare base64-encoded signatures
            if (!hash_equals($signature, $expectedSignature)) {
                error_log('AuthService: Signature mismatch');
                error_log('Header: ' . substr($header, 0, 30) . '...');
                error_log('Payload: ' . substr($payload, 0, 30) . '...');
                error_log('Expected sig (first 50): ' . substr($expectedSignature, 0, 50));
                error_log('Got sig (first 50): ' . substr($signature, 0, 50));
                return null;
            }
            
            // Decode payload (regular base64)
            $payloadDecoded = base64_decode($payload, true);
            if ($payloadDecoded === false) {
                error_log('AuthService: Failed to decode payload base64');
                return null;
            }
            
            $decoded = json_decode($payloadDecoded, true);
            
            if (!$decoded) {
                error_log('AuthService: Failed to decode payload JSON');
                return null;
            }
            
            // Check expiration
            if (isset($decoded['exp'])) {
                $now = time();
                $exp = $decoded['exp'];
                if ($exp < $now) {
                    error_log("AuthService: Token expired. Now: $now, Exp: $exp, Diff: " . ($now - $exp) . " seconds");
                    return null;
                }
            }
            
            return $decoded;
        } catch (Exception $e) {
            error_log('AuthService: Token verification exception: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get user from token
     */
    public function getUserFromToken($token) {
        $decoded = $this->verifyToken($token);
        if (!$decoded || !isset($decoded['user_id'])) {
            return null;
        }
        
        // Get user with role_name using database connection
        $db = $this->userModel->getDb();
        $stmt = $db->prepare(
            "SELECT u.*, r.name as role_name 
             FROM users u 
             JOIN roles r ON u.role_id = r.id 
             WHERE u.id = ?"
        );
        $stmt->execute([$decoded['user_id']]);
        $user = $stmt->fetch();
        
        if ($user) {
            unset($user['password_hash']);
        }
        
        return $user;
    }
    
    /**
     * Register new user
     */
    public function register($data) {
        // Check if email already exists
        $existingUser = $this->userModel->findByEmail($data['email']);
        if ($existingUser) {
            return ['success' => false, 'message' => 'Email already registered'];
        }
        
        // Validate password match
        if ($data['password'] !== $data['password_confirm']) {
            return ['success' => false, 'message' => 'Passwords do not match'];
        }
        
        // Hash password
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Get default role (customer/user role - usually ID 2, admin is 1)
        $defaultRoleId = 2; // Assuming 2 is the customer role
        
        // Create user
        $userData = [
            'email' => $data['email'],
            'password_hash' => $passwordHash,
            'full_name' => $data['full_name'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'role_id' => $defaultRoleId,
            'is_active' => 1
        ];
        
        try {
            $userId = $this->userModel->create($userData);
            
            // Get user with role_name using database connection
            $db = $this->userModel->getDb();
            $stmt = $db->prepare(
                "SELECT u.*, r.name as role_name 
                 FROM users u 
                 JOIN roles r ON u.role_id = r.id 
                 WHERE u.id = ?"
            );
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if ($user) {
                unset($user['password_hash']);
            }
            
            // Generate token
            $token = $this->generateToken($user);
            
            return [
                'success' => true,
                'user' => $user,
                'token' => $token
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Request password reset (sends code to email)
     */
    public function requestPasswordReset($email) {
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            // Don't reveal if email exists
            return ['success' => true, 'message' => 'If email exists, reset code has been sent'];
        }
        
        // Generate 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Save code as token (we'll use the token field for the code)
        $this->userModel->setPasswordResetToken($user['id'], $code, 30); // 30 minutes
        
        // Send email
        $emailService = new EmailService();
        $emailService->sendPasswordResetCode($user, $code);
        
        return ['success' => true, 'message' => 'Reset code sent to email'];
    }
    
    /**
     * Reset password with code
     */
    public function resetPassword($email, $code, $newPassword) {
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            return ['success' => false, 'message' => 'Invalid email'];
        }
        
        // Verify code
        $userWithToken = $this->userModel->findByResetToken($code);
        if (!$userWithToken || $userWithToken['id'] != $user['id']) {
            return ['success' => false, 'message' => 'Invalid or expired reset code'];
        }
        
        // Update password
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->userModel->updatePassword($user['id'], $passwordHash);
        
        // Clear reset token
        $this->userModel->clearPasswordResetToken($user['id']);
        
        return ['success' => true, 'message' => 'Password reset successful'];
    }
}

