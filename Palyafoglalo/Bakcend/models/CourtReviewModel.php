<?php
/**
 * Court Review Model
 * Handles court review/rating-related database operations
 */

class CourtReviewModel extends Model {
    protected $table = 'court_reviews';
    protected $primaryKey = 'id';
    protected $fillable = [
        'court_id', 'user_id', 'rating', 'title', 'review_text', 
        'is_verified', 'is_approved', 'is_active'
    ];
    
    /**
     * Get reviews for a court
     */
    public function findByCourtId($courtId, $options = []) {
        $activeOnly = $options['active_only'] ?? true;
        $approvedOnly = $options['approved_only'] ?? true;
        $limit = $options['limit'] ?? null;
        $offset = $options['offset'] ?? 0;
        
        $sql = "SELECT cr.*, u.full_name as user_name, u.email as user_email 
                FROM {$this->table} cr
                JOIN users u ON cr.user_id = u.id
                WHERE cr.court_id = ?";
        
        if ($activeOnly) {
            $sql .= " AND cr.is_active = 1";
        }
        
        if ($approvedOnly) {
            $sql .= " AND cr.is_approved = 1";
        }
        
        $sql .= " ORDER BY cr.created_at DESC";
        
        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
        }
        
        $stmt = $this->db->prepare($sql);
        $params = [$courtId];
        if ($limit !== null) {
            $params[] = $limit;
            $params[] = $offset;
        }
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Get average rating and total reviews for a court
     */
    public function getCourtStats($courtId) {
        $sql = "SELECT 
                    AVG(rating) as average_rating,
                    COUNT(*) as total_reviews,
                    COUNT(CASE WHEN rating = 5 THEN 1 END) as five_star,
                    COUNT(CASE WHEN rating = 4 THEN 1 END) as four_star,
                    COUNT(CASE WHEN rating = 3 THEN 1 END) as three_star,
                    COUNT(CASE WHEN rating = 2 THEN 1 END) as two_star,
                    COUNT(CASE WHEN rating = 1 THEN 1 END) as one_star
                FROM {$this->table}
                WHERE court_id = ? AND is_active = 1 AND is_approved = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courtId]);
        $stats = $stmt->fetch();
        
        if ($stats) {
            $stats['average_rating'] = $stats['average_rating'] ? round((float)$stats['average_rating'], 2) : 0;
            $stats['total_reviews'] = (int)$stats['total_reviews'];
        }
        
        return $stats ?: [
            'average_rating' => 0,
            'total_reviews' => 0,
            'five_star' => 0,
            'four_star' => 0,
            'three_star' => 0,
            'two_star' => 0,
            'one_star' => 0
        ];
    }
    
    /**
     * Check if user has already reviewed this court
     */
    public function userHasReviewed($courtId, $userId) {
        $sql = "SELECT {$this->primaryKey} FROM {$this->table} 
                WHERE court_id = ? AND user_id = ? AND is_active = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courtId, $userId]);
        return $stmt->fetch() !== false;
    }
    
    /**
     * Get user's review for a court
     */
    public function getUserReview($courtId, $userId) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE court_id = ? AND user_id = ? AND is_active = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courtId, $userId]);
        return $stmt->fetch();
    }
}

