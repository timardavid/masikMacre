<?php
/**
 * Court Controller
 * Handles court-related endpoints
 */

class CourtController extends BaseController {
    private $courtModel;
    private $surfaceModel;
    private $bookingModel;
    
    public function __construct() {
        $this->courtModel = new CourtModel();
        $this->surfaceModel = new SurfaceModel();
        $this->bookingModel = new BookingModel();
    }
    
    /**
     * GET /api/v1/courts
     */
    public function index() {
        $courts = $this->courtModel->findActive();
        
        // Include opening hours for each court
        foreach ($courts as &$court) {
            $court['opening_hours'] = $this->courtModel->getOpeningHours($court['id']);
        }
        
        $this->success($courts);
    }
    
    /**
     * GET /api/v1/courts/:id
     */
    public function show($id) {
        $court = $this->courtModel->findByIdWithDetails($id);
        
        if (!$court) {
            $this->error('Court not found', null, 404);
        }
        
        $court['opening_hours'] = $this->courtModel->getOpeningHours($id);
        
        $this->success($court);
    }
    
    /**
     * GET /api/v1/surfaces
     */
    public function surfaces() {
        $surfaces = $this->surfaceModel->findAll();
        $this->success($surfaces);
    }
    
    /**
     * GET /api/v1/courts/:id/availability
     */
    public function availability($courtId, $startDate = null, $endDate = null) {
        // Default to today and next 30 days
        $startDate = $startDate ?: date('Y-m-d');
        $endDate = $endDate ?: date('Y-m-d', strtotime('+30 days'));
        
        $bookings = $this->bookingModel->getCourtBookings($courtId, $startDate . ' 00:00:00', $endDate . ' 23:59:59');
        $blackouts = $this->courtModel->getBlackoutIntervals(
            $courtId,
            $startDate . ' 00:00:00',
            $endDate . ' 23:59:59'
        );
        
        $this->success([
            'court_id' => $courtId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'bookings' => $bookings,
            'blackouts' => $blackouts
        ]);
    }
}

