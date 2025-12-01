<?php
/**
 * Pricing Service
 * Handles pricing calculations and rules
 */

class PricingService {
    private $pricingModel;
    
    public function __construct() {
        $this->pricingModel = new PricingModel();
    }
    
    /**
     * Calculate price for a booking
     */
    public function calculatePrice($courtId, $startDatetime, $endDatetime) {
        return $this->pricingModel->calculatePrice($courtId, $startDatetime, $endDatetime);
    }
    
    /**
     * Get all active pricing rules
     */
    public function getAllPricingRules() {
        $stmt = Database::getInstance()->getConnection()->prepare(
            "SELECT * FROM pricing_rules WHERE is_active = 1 ORDER BY priority DESC, name"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get pricing rules for a specific court
     */
    public function getCourtPricingRules($courtId) {
        $stmt = Database::getInstance()->getConnection()->prepare(
            "SELECT * FROM pricing_rules 
             WHERE is_active = 1 
             AND (court_id = ? OR court_id IS NULL)
             ORDER BY priority DESC, name"
        );
        $stmt->execute([$courtId]);
        return $stmt->fetchAll();
    }
}

