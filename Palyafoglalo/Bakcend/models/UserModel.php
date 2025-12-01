<?php
/**
 * User Model
 * Handles user-related database operations
 */

class UserModel extends Model {
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = ['email', 'password_hash', 'full_name', 'phone', 'address', 'role_id', 'is_active', 'password_reset_token', 'password_reset_expires'];
    protected $hidden = ['password_hash'];
    
    /**
     * Find user by email
     */
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    /**
     * Find active users only
     */
    public function findActive($limit = null, $offset = 0) {
        $sql = "SELECT u.*, r.name as role_name 
                FROM {$this->table} u 
                JOIN roles r ON u.role_id = r.id 
                WHERE u.is_active = 1";
        
        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit, $offset]);
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }
        
        return $stmt->fetchAll();
    }
    
    /**
     * Update password
     */
    public function updatePassword($userId, $passwordHash) {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET password_hash = ? WHERE {$this->primaryKey} = ?"
        );
        return $stmt->execute([$passwordHash, $userId]);
    }
    
    /**
     * Verify password
     */
    public function verifyPassword($plainPassword, $hash) {
        return password_verify($plainPassword, $hash);
    }
    
    /**
     * Find user by password reset token
     */
    public function findByResetToken($token) {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} 
             WHERE password_reset_token = ? 
             AND password_reset_expires > NOW()"
        );
        $stmt->execute([$token]);
        return $stmt->fetch();
    }
    
    /**
     * Set password reset token
     */
    public function setPasswordResetToken($userId, $token, $expiresInMinutes = 60) {
        $expires = date('Y-m-d H:i:s', time() + ($expiresInMinutes * 60));
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} 
             SET password_reset_token = ?, password_reset_expires = ? 
             WHERE {$this->primaryKey} = ?"
        );
        return $stmt->execute([$token, $expires, $userId]);
    }
    
    /**
     * Clear password reset token
     */
    public function clearPasswordResetToken($userId) {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} 
             SET password_reset_token = NULL, password_reset_expires = NULL 
             WHERE {$this->primaryKey} = ?"
        );
        return $stmt->execute([$userId]);
    }
}

