<?php
/**
 * Surface Model
 * Handles surface type database operations
 */

class SurfaceModel extends Model {
    protected $table = 'surfaces';
    protected $primaryKey = 'id';
    protected $fillable = ['name'];
    
    /**
     * Find all surfaces ordered by name
     */
    public function findAll($filters = [], $limit = null, $offset = 0) {
        $sql = "SELECT * FROM {$this->table} ORDER BY name";
        $params = [];
        
        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}

