<?php
/**
 * Settings Model
 * JDW
 */

require_once __DIR__ . '/Database.php';

class Settings {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all settings
     */
    public function getAll() {
        try {
            $stmt = $this->db->query("SELECT * FROM settings ORDER BY setting_key");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Settings getAll error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get setting by key
     */
    public function getByKey($key) {
        try {
            $stmt = $this->db->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
            $stmt->execute([$key]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['setting_value'] : null;
        } catch (PDOException $e) {
            error_log("Settings getByKey error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get settings as associative array
     */
    public function getAsArray() {
        try {
            $stmt = $this->db->query("SELECT setting_key, setting_value FROM settings");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $settings = [];
            foreach ($results as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
            
            return $settings;
        } catch (PDOException $e) {
            error_log("Settings getAsArray error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update setting
     */
    public function update($key, $value) {
        try {
            $stmt = $this->db->prepare("
                UPDATE settings 
                SET setting_value = ?, updated_at = NOW() 
                WHERE setting_key = ?
            ");
            return $stmt->execute([$value, $key]);
        } catch (PDOException $e) {
            error_log("Settings update error: " . $e->getMessage());
            return false;
        }
    }
}

