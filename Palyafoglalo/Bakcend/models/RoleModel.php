<?php
/**
 * Role Model
 * Handles role-related database operations
 */

class RoleModel extends Model {
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $fillable = ['name'];
    
    /**
     * Find role by name
     */
    public function findByName($name) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE name = ?");
        $stmt->execute([$name]);
        return $stmt->fetch();
    }
}


