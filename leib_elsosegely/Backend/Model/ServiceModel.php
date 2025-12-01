<?php
require_once __DIR__ . '/../Config/Database.php';

class ServiceModel {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    public function getAllServices() {
        $stmt = $this->db->prepare("SELECT * FROM services WHERE is_active = 1 ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getServiceById($id) {
        $stmt = $this->db->prepare("SELECT * FROM services WHERE id = ? AND is_active = 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
