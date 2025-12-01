<?php
/**
 * Cleanup Expired Bookings Script
 * Deletes expired bookings that haven't been cancelled
 * Run this via cron job: php cleanup-expired-bookings.php
 * 
 * Example cron (runs daily at 2 AM):
 * 0 2 * * * /usr/bin/php /path/to/Bakcend/scripts/cleanup-expired-bookings.php
 */

require_once __DIR__ . '/../bootstrap.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Find expired bookings (end_datetime < now) that are not cancelled
    $sql = "SELECT id, customer_email, customer_name, start_datetime, end_datetime, price_cents, currency
            FROM bookings 
            WHERE end_datetime < NOW() 
            AND status NOT IN ('cancelled', 'completed')
            AND payment_status = 'unpaid'";
    
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $expiredBookings = $stmt->fetchAll();
    
    if (empty($expiredBookings)) {
        echo "No expired bookings to clean up.\n";
        exit(0);
    }
    
    echo "Found " . count($expiredBookings) . " expired booking(s) to delete.\n";
    
    $deletedCount = 0;
    
    foreach ($expiredBookings as $booking) {
        try {
            // Delete the booking
            $deleteStmt = $db->prepare("DELETE FROM bookings WHERE id = ?");
            $deleteStmt->execute([$booking['id']]);
            
            $deletedCount++;
            
            echo sprintf(
                "Deleted booking #%d for %s (%s - %s)\n",
                $booking['id'],
                $booking['customer_name'],
                $booking['start_datetime'],
                $booking['end_datetime']
            );
            
            // Log to audit log
            $auditStmt = $db->prepare(
                "INSERT INTO audit_log (action, entity_type, entity_id, description, performed_by_user_id)
                 VALUES ('delete', 'booking', ?, ?, NULL)"
            );
            $description = sprintf(
                "Auto-deleted expired booking for %s (end: %s). Payment: %d %s (not refunded as per policy).",
                $booking['customer_name'],
                $booking['end_datetime'],
                $booking['price_cents'] / 100,
                $booking['currency']
            );
            $auditStmt->execute([$booking['id'], $description]);
            
        } catch (Exception $e) {
            error_log("Failed to delete booking #{$booking['id']}: " . $e->getMessage());
            echo "ERROR: Failed to delete booking #{$booking['id']}\n";
        }
    }
    
    echo "\nCleanup completed. Deleted {$deletedCount} booking(s).\n";
    exit(0);
    
} catch (Exception $e) {
    error_log("Cleanup script error: " . $e->getMessage());
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

