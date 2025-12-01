<?php
/**
 * Court Review Service
 * Handles business logic for court reviews/ratings
 */

class CourtReviewService {
    private $reviewModel;
    private $courtModel;
    private $bookingModel;
    
    public function __construct() {
        $this->reviewModel = new CourtReviewModel();
        $this->courtModel = new CourtModel();
        $this->bookingModel = new BookingModel();
    }
    
    /**
     * Create a new review
     */
    public function createReview($data, $userId) {
        $errors = [];
        
        // Validate
        if (empty($data['court_id'])) {
            $errors[] = 'Court ID is required';
        }
        
        if (empty($data['rating']) || !in_array((int)$data['rating'], [1, 2, 3, 4, 5])) {
            $errors[] = 'Rating must be between 1 and 5';
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Check if court exists
        $court = $this->courtModel->findById($data['court_id']);
        if (!$court) {
            return ['success' => false, 'errors' => ['Court not found']];
        }
        
        // Check if user already reviewed this court
        $existingReview = $this->reviewModel->getUserReview($data['court_id'], $userId);
        if ($existingReview) {
            return ['success' => false, 'errors' => ['You have already reviewed this court']];
        }
        
        // Check if user has a verified booking (optional - for verified badge)
        $hasVerifiedBooking = $this->hasVerifiedBooking($data['court_id'], $userId);
        
        try {
            $reviewData = [
                'court_id' => $data['court_id'],
                'user_id' => $userId,
                'rating' => (int)$data['rating'],
                'title' => $data['title'] ?? null,
                'review_text' => $data['review_text'] ?? null,
                'is_verified' => $hasVerifiedBooking ? 1 : 0,
                'is_approved' => 1, // Auto-approve for now
                'is_active' => 1
            ];
            
            $reviewId = $this->reviewModel->create($reviewData);
            $review = $this->reviewModel->findById($reviewId);
            
            // Update court stats
            $this->updateCourtStats($data['court_id']);
            
            return ['success' => true, 'review' => $review];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['Failed to create review: ' . $e->getMessage()]];
        }
    }
    
    /**
     * Update a review
     */
    public function updateReview($reviewId, $data, $userId) {
        $review = $this->reviewModel->findById($reviewId);
        
        if (!$review) {
            return ['success' => false, 'errors' => ['Review not found']];
        }
        
        // Check ownership
        if ((int)$review['user_id'] !== (int)$userId) {
            return ['success' => false, 'errors' => ['You can only update your own reviews']];
        }
        
        $updateData = [];
        
        if (isset($data['rating']) && in_array((int)$data['rating'], [1, 2, 3, 4, 5])) {
            $updateData['rating'] = (int)$data['rating'];
        }
        
        if (isset($data['title'])) {
            $updateData['title'] = $data['title'];
        }
        
        if (isset($data['review_text'])) {
            $updateData['review_text'] = $data['review_text'];
        }
        
        try {
            $this->reviewModel->update($reviewId, $updateData);
            $updatedReview = $this->reviewModel->findById($reviewId);
            
            // Update court stats
            $this->updateCourtStats($review['court_id']);
            
            return ['success' => true, 'review' => $updatedReview];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['Failed to update review: ' . $e->getMessage()]];
        }
    }
    
    /**
     * Delete a review
     */
    public function deleteReview($reviewId, $userId) {
        $review = $this->reviewModel->findById($reviewId);
        
        if (!$review) {
            return ['success' => false, 'errors' => ['Review not found']];
        }
        
        // Check ownership
        if ((int)$review['user_id'] !== (int)$userId) {
            return ['success' => false, 'errors' => ['You can only delete your own reviews']];
        }
        
        try {
            $courtId = $review['court_id'];
            $this->reviewModel->update($reviewId, ['is_active' => 0]);
            
            // Update court stats
            $this->updateCourtStats($courtId);
            
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['Failed to delete review: ' . $e->getMessage()]];
        }
    }
    
    /**
     * Get reviews for a court
     */
    public function getCourtReviews($courtId, $options = []) {
        $reviews = $this->reviewModel->findByCourtId($courtId, $options);
        return ['success' => true, 'reviews' => $reviews];
    }
    
    /**
     * Get review stats for a court
     */
    public function getCourtStats($courtId) {
        $stats = $this->reviewModel->getCourtStats($courtId);
        return ['success' => true, 'stats' => $stats];
    }
    
    /**
     * Check if user has verified booking (for verified badge)
     */
    private function hasVerifiedBooking($courtId, $userId) {
        $sql = "SELECT COUNT(*) as count 
                FROM bookings 
                WHERE court_id = ? AND user_id = ? 
                AND status = 'confirmed' 
                AND end_datetime < NOW()";
        
        $db = $this->reviewModel->getDb();
        $stmt = $db->prepare($sql);
        $stmt->execute([$courtId, $userId]);
        $result = $stmt->fetch();
        
        return (int)$result['count'] > 0;
    }
    
    /**
     * Update court statistics (average rating, total reviews)
     */
    private function updateCourtStats($courtId) {
        $stats = $this->reviewModel->getCourtStats($courtId);
        
        $updateData = [
            'average_rating' => $stats['average_rating'],
            'total_reviews' => $stats['total_reviews']
        ];
        
        $this->courtModel->update($courtId, $updateData);
    }
}

