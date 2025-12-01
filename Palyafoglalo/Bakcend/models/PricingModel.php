<?php
/**
 * Pricing Model
 * Handles pricing rules and price calculations
 */

class PricingModel extends Model {
    protected $table = 'pricing_rules';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 'court_id', 'surface_id', 'is_indoor', 'weekday', 'is_weekend',
        'start_time', 'end_time', 'price_per_hour_cents', 'currency', 
        'is_active', 'priority', 'valid_from', 'valid_until'
    ];
    
    /**
     * Get applicable pricing rules for a booking
     */
    public function getApplicableRules($courtId, $startDatetime, $endDatetime) {
        $date = date('Y-m-d', strtotime($startDatetime));
        $time = date('H:i:s', strtotime($startDatetime));
        $weekday = date('w', strtotime($startDatetime)); // 0 = Sunday, 6 = Saturday
        $isWeekend = ($weekday == 0 || $weekday == 6) ? 1 : 0;
        
        // Get court details
        $courtModel = new CourtModel();
        $court = $courtModel->findById($courtId);
        if (!$court) {
            return [];
        }
        
        $sql = "SELECT * FROM {$this->table} 
                WHERE is_active = 1
                AND (valid_from IS NULL OR valid_from <= ?)
                AND (valid_until IS NULL OR valid_until >= ?)
                AND (
                    (court_id IS NULL OR court_id = ?) AND
                    (surface_id IS NULL OR surface_id = ?) AND
                    (is_indoor IS NULL OR is_indoor = ?) AND
                    (weekday IS NULL OR weekday = ?) AND
                    (is_weekend IS NULL OR is_weekend = ?) AND
                    (start_time IS NULL OR start_time <= ?) AND
                    (end_time IS NULL OR end_time >= ?)
                )
                ORDER BY priority DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $date, $date, // valid_from, valid_until
            $courtId, $court['surface_id'], $court['is_indoor'],
            $weekday, $isWeekend,
            $time, $time // start_time, end_time
        ]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Calculate price for a booking period
     */
    public function calculatePrice($courtId, $startDatetime, $endDatetime) {
        $rules = $this->getApplicableRules($courtId, $startDatetime, $endDatetime);
        
        if (empty($rules)) {
            return null; // No pricing rule found
        }
        
        // Use highest priority rule (first one after sorting)
        $rule = $rules[0];
        
        // Calculate hours (handle partial hours)
        $start = strtotime($startDatetime);
        $end = strtotime($endDatetime);
        $hours = ($end - $start) / 3600;
        
        // Round up to nearest 0.5 hours
        $hours = ceil($hours * 2) / 2;
        
        $totalCents = (int)($hours * $rule['price_per_hour_cents']);
        
        return [
            'price_cents' => $totalCents,
            'currency' => $rule['currency'],
            'hours' => $hours,
            'price_per_hour_cents' => $rule['price_per_hour_cents'],
            'rule_name' => $rule['name']
        ];
    }
}

