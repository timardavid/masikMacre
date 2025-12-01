<?php
require_once __DIR__ . '/../Config/Database.php';

class ContactModel {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    public function getContactInfo() {
        $stmt = $this->db->prepare("SELECT * FROM contact_info WHERE is_active = 1 LIMIT 1");
        $stmt->execute();
        return $stmt->fetch();
    }
    
    public function updateContactInfo($data) {
        $stmt = $this->db->prepare("UPDATE contact_info SET company_name = ?, owner_name = ?, email = ?, phone = ?, address = ?, description = ? WHERE id = 1");
        return $stmt->execute([
            $data['company_name'],
            $data['owner_name'],
            $data['email'],
            $data['phone'],
            $data['address'],
            $data['description']
        ]);
    }
}
