<?php
/**
 * Pricing Controller
 * Handles pricing-related endpoints
 */

class PricingController extends BaseController {
    private $pricingService;
    
    public function __construct() {
        $this->pricingService = new PricingService();
    }
    
    /**
     * GET /api/v1/pricing/rules
     */
    public function index() {
        $rules = $this->pricingService->getAllPricingRules();
        $this->success($rules);
    }
    
    /**
     * GET /api/v1/pricing/rules/court/:courtId
     */
    public function courtRules($courtId) {
        $rules = $this->pricingService->getCourtPricingRules($courtId);
        $this->success($rules);
    }
    
    /**
     * GET /api/v1/pricing/calculate
     */
    public function calculate() {
        $params = $this->getQueryParams();
        
        if (!isset($params['court_id']) || !isset($params['start_datetime']) || !isset($params['end_datetime'])) {
            $this->error('Missing required parameters: court_id, start_datetime, end_datetime', null, 400);
        }
        
        $pricing = $this->pricingService->calculatePrice(
            $params['court_id'],
            $params['start_datetime'],
            $params['end_datetime']
        );
        
        if (!$pricing) {
            $this->error('Could not calculate price for this booking', null, 400);
        }
        
        $this->success($pricing);
    }
}

