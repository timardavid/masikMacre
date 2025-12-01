<?php
/**
 * Booking Rule Model
 * Handles system-wide booking rules/constraints
 */

class BookingRuleModel extends Model {
    protected $table = 'booking_rules';
    protected $primaryKey = 'id';
    protected $fillable = ['key_name', 'value', 'description', 'updated_by_user_id'];
    
    /**
     * Get rule value by key
     */
    public function getRule($keyName, $default = null) {
        $stmt = $this->db->prepare(
            "SELECT value FROM {$this->table} WHERE key_name = ?"
        );
        $stmt->execute([$keyName]);
        $result = $stmt->fetch();
        
        return $result ? $result['value'] : $default;
    }
    
    /**
     * Get all rules as key-value array
     */
    public function getAllRules() {
        $stmt = $this->db->prepare("SELECT key_name, value, description FROM {$this->table}");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        
        $rules = [];
        foreach ($rows as $row) {
            $rules[$row['key_name']] = [
                'value' => $row['value'],
                'description' => $row['description']
            ];
        }
        
        return $rules;
    }
    
    /**
     * Set or update rule
     */
    public function setRule($keyName, $value, $description = null, $userId = null) {
        // Check if exists
        $existing = $this->query(
            "SELECT id FROM {$this->table} WHERE key_name = ?",
            [$keyName]
        )->fetch();
        
        if ($existing) {
            $stmt = $this->db->prepare(
                "UPDATE {$this->table} SET value = ?, description = ?, updated_by_user_id = ? WHERE key_name = ?"
            );
            return $stmt->execute([$value, $description, $userId, $keyName]);
        } else {
            $stmt = $this->db->prepare(
                "INSERT INTO {$this->table} (key_name, value, description, updated_by_user_id) VALUES (?, ?, ?, ?)"
            );
            return $stmt->execute([$keyName, $value, $description, $userId]);
        }
    }
}

