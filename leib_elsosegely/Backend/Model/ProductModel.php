<?php
require_once __DIR__ . '/../Config/Database.php';

class ProductModel {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    public function getAllProducts() {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE is_active = 1 ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getProductsByCategory($category) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE category = ? AND is_active = 1 ORDER BY name ASC");
        $stmt->execute([$category]);
        return $stmt->fetchAll();
    }
    
    public function getProductById($id) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ? AND is_active = 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getCategories() {
        $stmt = $this->db->prepare("SELECT DISTINCT category FROM products WHERE is_active = 1 AND category IS NOT NULL ORDER BY category ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
