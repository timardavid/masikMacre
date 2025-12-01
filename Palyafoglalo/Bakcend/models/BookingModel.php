<?php
/**
 * Booking Model
 * Handles booking-related database operations
 */

class BookingModel extends Model {
    protected $table = 'bookings';
    protected $primaryKey = 'id';
    protected $fillable = [
        'court_id', 'user_id', 'customer_name', 'customer_phone', 'customer_email',
        'start_datetime', 'end_datetime', 'status', 'price_cents', 'currency', 'payment_status',
        'cancellation_token'
    ];
    
    /**
     * Find bookings with filters
     */
    public function findWithFilters($filters = [], $limit = null, $offset = 0) {
        $sql = "SELECT b.*, c.name as court_name, u.full_name as created_by_name 
                FROM {$this->table} b 
                LEFT JOIN courts c ON b.court_id = c.id 
                LEFT JOIN users u ON b.user_id = u.id 
                WHERE 1=1";
        
        $params = [];
        
        if (isset($filters['court_id'])) {
            $sql .= " AND b.court_id = ?";
            $params[] = $filters['court_id'];
        }
        
        // Filter by status - if status is specified, use it, otherwise exclude cancelled
        if (isset($filters['status'])) {
            $sql .= " AND b.status = ?";
            $params[] = $filters['status'];
        } else {
            // By default, exclude cancelled bookings for availability checking
            $sql .= " AND b.status <> 'cancelled'";
        }
        
        if (isset($filters['user_id'])) {
            $sql .= " AND b.user_id = ?";
            $params[] = $filters['user_id'];
        }
        
        if (isset($filters['customer_email'])) {
            $sql .= " AND b.customer_email = ?";
            $params[] = $filters['customer_email'];
        }
        
        if (isset($filters['start_date'])) {
            $sql .= " AND DATE(b.start_datetime) >= ?";
            $params[] = $filters['start_date'];
        }
        
        if (isset($filters['end_date'])) {
            $sql .= " AND DATE(b.end_datetime) <= ?";
            $params[] = $filters['end_date'];
        }
        
        $sql .= " ORDER BY b.start_datetime DESC";
        
        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Check if court is available for given time period
     */
    public function isCourtAvailable($courtId, $startDatetime, $endDatetime, $excludeBookingId = null) {
        $sql = "SELECT COUNT(*) as count 
                FROM {$this->table} 
                WHERE court_id = ? 
                AND status <> 'cancelled'
                AND start_datetime < ? 
                AND end_datetime > ?";
        
        $params = [$courtId, $endDatetime, $startDatetime];
        
        if ($excludeBookingId !== null) {
            $sql .= " AND {$this->primaryKey} <> ?";
            $params[] = $excludeBookingId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return (int)$result['count'] === 0;
    }
    
    /**
     * Get bookings for a court within date range
     */
    public function getCourtBookings($courtId, $startDate, $endDate) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE court_id = ? 
                AND status <> 'cancelled'
                AND start_datetime >= ? 
                AND end_datetime <= ?
                ORDER BY start_datetime";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courtId, $startDate, $endDate]);
        return $stmt->fetchAll();
    }
    
    /**
     * Update booking status
     */
    public function updateStatus($bookingId, $status) {
        $allowedStatuses = ['pending', 'confirmed', 'cancelled', 'completed', 'no_show'];
        if (!in_array($status, $allowedStatuses)) {
            throw new InvalidArgumentException("Invalid status: {$status}");
        }
        
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET status = ? WHERE {$this->primaryKey} = ?"
        );
        return $stmt->execute([$status, $bookingId]);
    }
    
    /**
     * Find booking by cancellation token
     */
    public function findByCancellationToken($token) {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE cancellation_token = ? AND status NOT IN ('cancelled', 'completed')"
        );
        $stmt->execute([$token]);
        return $stmt->fetch();
    }
}

