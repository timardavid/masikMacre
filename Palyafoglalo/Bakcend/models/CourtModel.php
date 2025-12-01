<?php
/**
 * Court Model
 * Handles court-related database operations
 */

class CourtModel extends Model {
    protected $table = 'courts';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 'surface_id', 'is_indoor', 'has_lighting', 'notes', 'is_active',
        'main_image_url', 'description', 'capacity', 'dimensions', 'facilities',
        'parking_available', 'changing_rooms', 'pro_shop', 'average_rating', 'total_reviews'
    ];
    
    /**
     * Find active courts with surface information
     */
    public function findActive() {
        $sql = "SELECT c.*, s.name as surface_name 
                FROM {$this->table} c 
                JOIN surfaces s ON c.surface_id = s.id 
                WHERE c.is_active = 1 
                ORDER BY c.name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $courts = $stmt->fetchAll();
        
        // Include images and review stats for each court
        $imageModel = new CourtImageModel();
        $reviewModel = new CourtReviewModel();
        
        foreach ($courts as &$court) {
            // Get main image or first image
            $mainImage = $imageModel->getMainImage($court['id']);
            if ($mainImage) {
                $court['main_image_url'] = $mainImage['image_url'];
            }
            // Get all images (for detail page)
            $court['images'] = $imageModel->findByCourtId($court['id'], true);
            // Get review stats
            $court['review_stats'] = $reviewModel->getCourtStats($court['id']);
        }
        
        return $courts;
    }
    
    /**
     * Find court by ID with related data
     */
    public function findByIdWithDetails($id) {
        $sql = "SELECT c.*, s.name as surface_name 
                FROM {$this->table} c 
                JOIN surfaces s ON c.surface_id = s.id 
                WHERE c.{$this->primaryKey} = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $court = $stmt->fetch();
        
        if ($court) {
            // Get images
            $imageModel = new CourtImageModel();
            $court['images'] = $imageModel->findByCourtId($id);
            
            // Get reviews stats
            $reviewModel = new CourtReviewModel();
            $court['review_stats'] = $reviewModel->getCourtStats($id);
        }
        
        return $court;
    }
    
    /**
     * Get opening hours for a court
     */
    public function getOpeningHours($courtId) {
        $sql = "SELECT weekday, is_closed, open_time, close_time 
                FROM court_opening_hours 
                WHERE court_id = ? 
                ORDER BY weekday";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courtId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get blackout intervals for a court (including global ones)
     */
    public function getBlackoutIntervals($courtId, $startDate, $endDate) {
        $sql = "SELECT * FROM blackout_intervals 
                WHERE (court_id = ? OR court_id IS NULL)
                AND start_datetime >= ? 
                AND end_datetime <= ?
                ORDER BY start_datetime";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courtId, $startDate, $endDate]);
        return $stmt->fetchAll();
    }
}

