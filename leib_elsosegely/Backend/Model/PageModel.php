<?php
require_once __DIR__ . '/../Config/Database.php';

class PageModel {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    public function getPageBySlug($slug) {
        $stmt = $this->db->prepare("SELECT * FROM pages WHERE slug = ? AND is_active = 1");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }
    
    public function getAllPages() {
        $stmt = $this->db->prepare("SELECT * FROM pages WHERE is_active = 1 ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getHomePage() {
        return $this->getPageBySlug('bemutatkozas');
    }
}
