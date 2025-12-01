<?php
/**
 * Products Model (Pricing Packages)
 * JDW
 */

require_once __DIR__ . '/Database.php';

class Products {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all active products
     */
    public function getAll() {
        try {
            $stmt = $this->db->query("
                SELECT * FROM products 
                WHERE status = 'active' 
                ORDER BY price ASC
            ");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Parse JSON fields
            foreach ($products as &$product) {
                if ($product['features']) {
                    $product['features'] = json_decode($product['features'], true);
                }
                if ($product['gallery_images']) {
                    $product['gallery_images'] = json_decode($product['gallery_images'], true);
                }
            }
            
            return $products;
        } catch (PDOException $e) {
            error_log("Products getAll error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get product by ID
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ? AND status = 'active'");
            $stmt->execute([$id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($product) {
                // Parse JSON fields
                if ($product['features']) {
                    $product['features'] = json_decode($product['features'], true);
                }
                if ($product['gallery_images']) {
                    $product['gallery_images'] = json_decode($product['gallery_images'], true);
                }
            }
            
            return $product;
        } catch (PDOException $e) {
            error_log("Products getById error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get product by slug
     */
    public function getBySlug($slug) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM products WHERE slug = ? AND status = 'active'");
            $stmt->execute([$slug]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($product) {
                // Parse JSON fields
                if ($product['features']) {
                    $product['features'] = json_decode($product['features'], true);
                }
                if ($product['gallery_images']) {
                    $product['gallery_images'] = json_decode($product['gallery_images'], true);
                }
            }
            
            return $product;
        } catch (PDOException $e) {
            error_log("Products getBySlug error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get featured products
     */
    public function getFeatured() {
        try {
            $stmt = $this->db->query("
                SELECT * FROM products 
                WHERE status = 'active' AND is_featured = TRUE 
                ORDER BY price ASC
            ");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Parse JSON fields
            foreach ($products as &$product) {
                if ($product['features']) {
                    $product['features'] = json_decode($product['features'], true);
                }
            }
            
            return $products;
        } catch (PDOException $e) {
            error_log("Products getFeatured error: " . $e->getMessage());
            return [];
        }
    }
}

