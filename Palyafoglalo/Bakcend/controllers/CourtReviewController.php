<?php
/**
 * Court Review Controller
 * Handles court review/rating endpoints
 */

class CourtReviewController extends BaseController {
    private $reviewService;
    
    public function __construct() {
        $this->reviewService = new CourtReviewService();
    }
    
    /**
     * GET /api/v1/courts/:id/reviews
     */
    public function index($courtId) {
        $params = $this->getQueryParams();
        $options = [
            'limit' => isset($params['limit']) ? (int)$params['limit'] : 50,
            'offset' => isset($params['offset']) ? (int)$params['offset'] : 0,
            'active_only' => true,
            'approved_only' => true
        ];
        
        $result = $this->reviewService->getCourtReviews($courtId, $options);
        $this->success($result);
    }
    
    /**
     * GET /api/v1/courts/:id/reviews/stats
     */
    public function stats($courtId) {
        $result = $this->reviewService->getCourtStats($courtId);
        $this->success($result['stats']);
    }
    
    /**
     * POST /api/v1/courts/:id/reviews
     */
    public function create($courtId) {
        $user = $this->getCurrentUser();
        if (!$user) {
            $this->error('Authentication required', ['Please login to leave a review'], 401);
        }
        
        $data = $this->getRequestBody();
        $data['court_id'] = $courtId;
        
        $result = $this->reviewService->createReview($data, $user['id']);
        
        if ($result['success']) {
            $this->success($result['review'], 'Review created successfully', 201);
        } else {
            $this->error('Failed to create review', $result['errors'], 400);
        }
    }
    
    /**
     * PUT /api/v1/reviews/:id
     */
    public function update($reviewId) {
        $user = $this->getCurrentUser();
        if (!$user) {
            $this->error('Authentication required', ['Please login to update a review'], 401);
        }
        
        $data = $this->getRequestBody();
        $result = $this->reviewService->updateReview($reviewId, $data, $user['id']);
        
        if ($result['success']) {
            $this->success($result['review'], 'Review updated successfully');
        } else {
            $this->error('Failed to update review', $result['errors'], 400);
        }
    }
    
    /**
     * DELETE /api/v1/reviews/:id
     */
    public function delete($reviewId) {
        $user = $this->getCurrentUser();
        if (!$user) {
            $this->error('Authentication required', ['Please login to delete a review'], 401);
        }
        
        $result = $this->reviewService->deleteReview($reviewId, $user['id']);
        
        if ($result['success']) {
            $this->success(null, 'Review deleted successfully');
        } else {
            $this->error('Failed to delete review', $result['errors'], 400);
        }
    }
}

