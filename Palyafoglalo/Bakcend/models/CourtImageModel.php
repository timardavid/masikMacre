<?php
/**
 * Court Image Model
 * Handles court image-related database operations
 */

class CourtImageModel extends Model {
    protected $table = 'court_images';
    protected $primaryKey = 'id';
    protected $fillable = [
        'court_id', 'image_url', 'image_path', 'alt_text', 'display_order', 'is_active'
    ];
    
    /**
     * Get all images for a court
     */
    public function findByCourtId($courtId, $activeOnly = true) {
        $sql = "SELECT * FROM {$this->table} WHERE court_id = ?";
        
        if ($activeOnly) {
            $sql .= " AND is_active = 1";
        }
        
        $sql .= " ORDER BY display_order ASC, created_at ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courtId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get main image for a court
     */
    public function getMainImage($courtId) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE court_id = ? AND is_active = 1 
                ORDER BY display_order ASC, created_at ASC 
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courtId]);
        return $stmt->fetch();
    }
    
    /**
     * Delete image by ID
     */
    public function deleteById($id) {
        // Get image info before deleting
        $image = $this->findById($id);
        
        if ($image && $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?")) {
            $stmt->execute([$id]);
            return $image;
        }
        
        return null;
    }
}

