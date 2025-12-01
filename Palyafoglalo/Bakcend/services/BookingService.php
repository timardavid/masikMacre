<?php
/**
 * Booking Service
 * Handles business logic for bookings
 */

class BookingService {
    private $bookingModel;
    private $courtModel;
    private $pricingModel;
    private $bookingRuleModel;
    
    public function __construct() {
        $this->bookingModel = new BookingModel();
        $this->courtModel = new CourtModel();
        $this->pricingModel = new PricingModel();
        $this->bookingRuleModel = new BookingRuleModel();
    }
    
    /**
     * Validate booking constraints
     */
    private function validateBookingConstraints($courtId, $startDatetime, $endDatetime) {
        $errors = [];
        
        // Check time order
        if (strtotime($startDatetime) >= strtotime($endDatetime)) {
            $errors[] = 'End time must be after start time';
        }
        
        // Check minimum booking duration
        $minDuration = (int)$this->bookingRuleModel->getRule('min_booking_duration_minutes', 60);
        $duration = (strtotime($endDatetime) - strtotime($startDatetime)) / 60;
        if ($duration < $minDuration) {
            $errors[] = "Minimum booking duration is {$minDuration} minutes";
        }
        
        // Check maximum booking duration
        $maxDuration = (int)$this->bookingRuleModel->getRule('max_booking_duration_minutes', 240);
        if ($duration > $maxDuration) {
            $errors[] = "Maximum booking duration is {$maxDuration} minutes";
        }
        
        // Check advance booking limit
        $maxDays = (int)$this->bookingRuleModel->getRule('max_days_in_advance', 90);
        $daysDiff = (strtotime($startDatetime) - time()) / 86400;
        if ($daysDiff > $maxDays) {
            $errors[] = "Cannot book more than {$maxDays} days in advance";
        }
        
        // Check minimum hours before booking
        $minHours = (int)$this->bookingRuleModel->getRule('min_hours_before_booking', 2);
        $hoursDiff = (strtotime($startDatetime) - time()) / 3600;
        if ($hoursDiff < $minHours) {
            $errors[] = "Must book at least {$minHours} hours in advance";
        }
        
        // Check if court exists and is active
        $court = $this->courtModel->findById($courtId);
        if (!$court) {
            $errors[] = 'Court not found';
        } elseif (!$court['is_active']) {
            $errors[] = 'Court is not active';
        }
        
        return $errors;
    }
    
    /**
     * Check if court is available
     */
    public function checkAvailability($courtId, $startDatetime, $endDatetime, $excludeBookingId = null) {
        // Check booking constraints
        $errors = $this->validateBookingConstraints($courtId, $startDatetime, $endDatetime);
        if (!empty($errors)) {
            return ['available' => false, 'errors' => $errors];
        }
        
        // Check for overlapping bookings
        $available = $this->bookingModel->isCourtAvailable(
            $courtId, 
            $startDatetime, 
            $endDatetime, 
            $excludeBookingId
        );
        
        if (!$available) {
            $errors[] = 'Court is already booked for this time period';
        }
        
        // Check blackout intervals
        $blackouts = $this->courtModel->getBlackoutIntervals(
            $courtId, 
            $startDatetime, 
            $endDatetime
        );
        if (!empty($blackouts)) {
            $available = false;
            $errors[] = 'Court is unavailable due to maintenance or events';
        }
        
        return [
            'available' => $available && empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Create a new booking
     */
    public function createBooking($data, $userId = null) {
        // Validate
        $validation = $this->checkAvailability(
            $data['court_id'],
            $data['start_datetime'],
            $data['end_datetime']
        );
        
        if (!$validation['available']) {
            return [
                'success' => false,
                'errors' => $validation['errors']
            ];
        }
        
        // Calculate duration in hours
        $startTime = strtotime($data['start_datetime']);
        $endTime = strtotime($data['end_datetime']);
        $durationHours = ($endTime - $startTime) / 3600;
        
        // Fixed prices per hour (in cents)
        $fixedPrices = [
            1 => 9000 * 100,   // 1 hour = 9000 HUF
            2 => 16000 * 100,  // 2 hours = 16000 HUF
            3 => 20000 * 100,  // 3 hours = 20000 HUF
        ];
        
        // Round to nearest hour and use fixed price
        $roundedHours = round($durationHours);
        if ($roundedHours < 1) $roundedHours = 1;
        if ($roundedHours > 3) $roundedHours = 3;
        
        $priceCents = $fixedPrices[$roundedHours] ?? $fixedPrices[1];
        
        $pricing = [
            'price_cents' => $priceCents,
            'currency' => 'HUF',
            'hours' => $roundedHours
        ];
        
        // Generate cancellation token
        $cancellationToken = bin2hex(random_bytes(32));
        
        // Prepare booking data
        $bookingData = [
            'court_id' => $data['court_id'],
            'user_id' => $userId,
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'] ?? null,
            'customer_email' => $data['customer_email'] ?? null,
            'start_datetime' => $data['start_datetime'],
            'end_datetime' => $data['end_datetime'],
            'status' => $this->bookingRuleModel->getRule('auto_confirm_enabled', '1') === '1' 
                        ? 'confirmed' 
                        : 'pending',
            'price_cents' => $pricing['price_cents'],
            'currency' => $pricing['currency'],
            'payment_status' => 'unpaid',
            'cancellation_token' => $cancellationToken
        ];
        
        try {
            $bookingId = $this->bookingModel->create($bookingData);
            $booking = $this->bookingModel->findById($bookingId);
            
            // Send confirmation email with cancellation link
            $emailInfo = null;
            if (!empty($booking['customer_email'])) {
                try {
                    $emailService = new EmailService();
                    $emailResult = $emailService->sendBookingConfirmation($booking, $cancellationToken);
                    
                    // Log email result (success or saved to file)
                    if (isset($emailResult['email_file'])) {
                        $logMessage = "📧 Email saved to: Bakcend/logs/{$emailResult['email_file']}";
                        error_log($logMessage);
                        $emailInfo = [
                            'file' => $emailResult['email_file'],
                            'path' => '/Palyafoglalo/Bakcend/logs/' . $emailResult['email_file'],
                            'message' => $emailResult['message']
                        ];
                    }
                } catch (Exception $e) {
                    // Log error but don't fail booking creation
                    error_log('Failed to send confirmation email: ' . $e->getMessage());
                    $emailInfo = [
                        'error' => 'Failed to save email: ' . $e->getMessage()
                    ];
                }
            }
            
            return [
                'success' => true,
                'booking' => $booking,
                'pricing' => $pricing,
                'email' => $emailInfo
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'errors' => ['Failed to create booking: ' . $e->getMessage()]
            ];
        }
    }
    
    /**
     * Update booking
     */
    public function updateBooking($bookingId, $data, $userId = null) {
        $booking = $this->bookingModel->findById($bookingId);
        if (!$booking) {
            return ['success' => false, 'errors' => ['Booking not found']];
        }
        
        // If time changed, check availability
        if (isset($data['start_datetime']) || isset($data['end_datetime'])) {
            $start = $data['start_datetime'] ?? $booking['start_datetime'];
            $end = $data['end_datetime'] ?? $booking['end_datetime'];
            
            $validation = $this->checkAvailability(
                $data['court_id'] ?? $booking['court_id'],
                $start,
                $end,
                $bookingId
            );
            
            if (!$validation['available']) {
                return [
                    'success' => false,
                    'errors' => $validation['errors']
                ];
            }
        }
        
        // Recalculate price if time or court changed
        if (isset($data['start_datetime']) || isset($data['end_datetime']) || isset($data['court_id'])) {
            $pricing = $this->pricingModel->calculatePrice(
                $data['court_id'] ?? $booking['court_id'],
                $data['start_datetime'] ?? $booking['start_datetime'],
                $data['end_datetime'] ?? $booking['end_datetime']
            );
            
            if ($pricing) {
                $data['price_cents'] = $pricing['price_cents'];
                $data['currency'] = $pricing['currency'];
            }
        }
        
        try {
            $this->bookingModel->update($bookingId, $data);
            $updated = $this->bookingModel->findById($bookingId);
            
            return ['success' => true, 'booking' => $updated];
        } catch (Exception $e) {
            return [
                'success' => false,
                'errors' => ['Failed to update booking: ' . $e->getMessage()]
            ];
        }
    }
    
    /**
     * Cancel booking
     * No time restrictions - can cancel anytime before start
     */
    public function cancelBooking($bookingId, $userId = null) {
        $booking = $this->bookingModel->findById($bookingId);
        if (!$booking) {
            return ['success' => false, 'errors' => ['Booking not found']];
        }
        
        // Only allow cancellation if booking hasn't started yet
        $startTime = strtotime($booking['start_datetime']);
        if ($startTime < time()) {
            return [
                'success' => false,
                'errors' => ['Cannot cancel a booking that has already started or passed']
            ];
        }
        
        // Only allow cancellation of confirmed or pending bookings
        if (!in_array($booking['status'], ['confirmed', 'pending'])) {
            return [
                'success' => false,
                'errors' => ['Only confirmed or pending bookings can be cancelled']
            ];
        }
        
        try {
            $this->bookingModel->updateStatus($bookingId, 'cancelled');
            return ['success' => true];
        } catch (Exception $e) {
            return [
                'success' => false,
                'errors' => ['Failed to cancel booking: ' . $e->getMessage()]
            ];
        }
    }
}

