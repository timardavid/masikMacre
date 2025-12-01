<?php
/**
 * Booking Controller
 * Handles booking-related endpoints
 */

class BookingController extends BaseController {
    private $bookingService;
    private $bookingModel;
    private $pricingService;
    
    public function __construct() {
        $this->bookingService = new BookingService();
        $this->bookingModel = new BookingModel();
        $this->pricingService = new PricingService();
    }
    
    /**
     * GET /api/v1/bookings
     */
    public function index() {
        $params = $this->getQueryParams();
        $limit = isset($params['limit']) ? (int)$params['limit'] : 20;
        $offset = isset($params['offset']) ? (int)$params['offset'] : 0;
        
        $filters = [];
        if (isset($params['court_id'])) $filters['court_id'] = $params['court_id'];
        if (isset($params['status'])) $filters['status'] = $params['status'];
        if (isset($params['start_date'])) $filters['start_date'] = $params['start_date'];
        if (isset($params['end_date'])) $filters['end_date'] = $params['end_date'];
        
        $bookings = $this->bookingModel->findWithFilters($filters, $limit, $offset);
        
        $this->success([
            'bookings' => $bookings,
            'limit' => $limit,
            'offset' => $offset,
            'count' => count($bookings)
        ]);
    }
    
    /**
     * GET /api/v1/bookings/:id
     */
    public function show($id) {
        $booking = $this->bookingModel->findById($id);
        
        if (!$booking) {
            $this->error('Booking not found', null, 404);
        }
        
        $this->success($booking);
    }
    
    /**
     * POST /api/v1/bookings
     */
    public function create() {
        $data = $this->getRequestBody();
        
        $errors = $this->validateRequired($data, [
            'court_id', 'customer_name', 'start_datetime', 'end_datetime'
        ]);
        
        if (!empty($errors)) {
            $this->error('Validation failed', $errors, 400);
        }
        
        // Require authentication - user must be logged in
        $user = $this->getCurrentUser();
        if (!$user) {
            $this->error('Authentication required', ['Please login to create a booking'], 401);
        }
        
        // Use authenticated user's ID
        $userId = $user['id'];
        
        // Use user's email if not provided
        if (empty($data['customer_email']) && !empty($user['email'])) {
            $data['customer_email'] = $user['email'];
        }
        
        // Use user's name if not provided
        if (empty($data['customer_name']) && !empty($user['full_name'])) {
            $data['customer_name'] = $user['full_name'];
        }
        
        $result = $this->bookingService->createBooking($data, $userId);
        
        if ($result['success']) {
            $this->success($result['booking'], 'Booking created successfully', 201);
        } else {
            $this->error('Failed to create booking', $result['errors'], 400);
        }
    }
    
    /**
     * PUT /api/v1/bookings/:id
     */
    public function update($id) {
        $data = $this->getRequestBody();
        
        // Get user from token if available
        $user = $this->getCurrentUser();
        $userId = $user ? $user['id'] : null;
        
        $result = $this->bookingService->updateBooking($id, $data, $userId);
        
        if ($result['success']) {
            $this->success($result['booking'], 'Booking updated successfully');
        } else {
            $this->error('Failed to update booking', $result['errors'], 400);
        }
    }
    
    /**
     * DELETE /api/v1/bookings/:id
     */
    public function cancel($id) {
        $user = $this->getCurrentUser();
        $userId = $user ? $user['id'] : null;
        
        $result = $this->bookingService->cancelBooking($id, $userId);
        
        if ($result['success']) {
            $this->success(null, 'Booking cancelled successfully');
        } else {
            $this->error('Failed to cancel booking', $result['errors'], 400);
        }
    }
    
    /**
     * GET /api/v1/bookings/availability
     */
    public function checkAvailability() {
        $params = $this->getQueryParams();
        
        if (!isset($params['court_id']) || !isset($params['start_datetime']) || !isset($params['end_datetime'])) {
            $this->error('Missing required parameters: court_id, start_datetime, end_datetime', null, 400);
        }
        
        $result = $this->bookingService->checkAvailability(
            $params['court_id'],
            $params['start_datetime'],
            $params['end_datetime'],
            $params['exclude_booking_id'] ?? null
        );
        
        $this->success($result);
    }
    
    /**
     * GET /api/v1/bookings/calculate-price
     */
    public function calculatePrice() {
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

