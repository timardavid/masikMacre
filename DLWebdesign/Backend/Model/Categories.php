<?php
/**
 * Categories Model
 * JDW
 */

require_once __DIR__ . '/Database.php';

class Categories {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all active categories
     */
    public function getAll() {
        try {
            $stmt = $this->db->query("
                SELECT * FROM categories 
                WHERE status = 'active' 
                ORDER BY display_order ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Categories getAll error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get category by ID
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = ? AND status = 'active'");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Categories getById error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get category by slug
     */
    public function getBySlug($slug) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM categories WHERE slug = ? AND status = 'active'");
            $stmt->execute([$slug]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Categories getBySlug error: " . $e->getMessage());
            return null;
        }
    }
}

