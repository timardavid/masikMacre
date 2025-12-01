<?php
/**
 * Portfolio Model
 * JDW
 */

require_once __DIR__ . '/Database.php';

class Portfolio {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all active portfolio items
     */
    public function getAll() {
        try {
            $stmt = $this->db->query("
                SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM portfolio p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active'
                ORDER BY p.display_order ASC, p.created_at DESC
            ");
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Parse JSON fields
            foreach ($items as &$item) {
                if ($item['gallery_images']) {
                    $item['gallery_images'] = json_decode($item['gallery_images'], true);
                }
                if ($item['technologies']) {
                    $item['technologies'] = json_decode($item['technologies'], true);
                }
            }
            
            return $items;
        } catch (PDOException $e) {
            error_log("Portfolio getAll error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get portfolio item by ID
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM portfolio p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id = ? AND p.status = 'active'
            ");
            $stmt->execute([$id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($item) {
                // Parse JSON fields
                if ($item['gallery_images']) {
                    $item['gallery_images'] = json_decode($item['gallery_images'], true);
                }
                if ($item['technologies']) {
                    $item['technologies'] = json_decode($item['technologies'], true);
                }
            }
            
            return $item;
        } catch (PDOException $e) {
            error_log("Portfolio getById error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get portfolio by category
     */
    public function getByCategory($category_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM portfolio p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.category_id = ? AND p.status = 'active'
                ORDER BY p.display_order ASC, p.created_at DESC
            ");
            $stmt->execute([$category_id]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Parse JSON fields
            foreach ($items as &$item) {
                if ($item['gallery_images']) {
                    $item['gallery_images'] = json_decode($item['gallery_images'], true);
                }
                if ($item['technologies']) {
                    $item['technologies'] = json_decode($item['technologies'], true);
                }
            }
            
            return $items;
        } catch (PDOException $e) {
            error_log("Portfolio getByCategory error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get featured portfolio items
     */
    public function getFeatured($limit = 6) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM portfolio p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active' AND p.is_featured = TRUE
                ORDER BY p.display_order ASC, p.created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Parse JSON fields
            foreach ($items as &$item) {
                if ($item['gallery_images']) {
                    $item['gallery_images'] = json_decode($item['gallery_images'], true);
                }
                if ($item['technologies']) {
                    $item['technologies'] = json_decode($item['technologies'], true);
                }
            }
            
            return $items;
        } catch (PDOException $e) {
            error_log("Portfolio getFeatured error: " . $e->getMessage());
            return [];
        }
    }
}

