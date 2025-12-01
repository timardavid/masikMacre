<?php
/**
 * Email Service
 * Handles email sending functionality
 */

class EmailService {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Send booking confirmation email with cancellation link
     */
    public function sendBookingConfirmation($booking, $cancellationToken) {
        if (empty($booking['customer_email'])) {
            return ['success' => false, 'message' => 'No email address provided'];
        }
        
        $courtModel = new CourtModel();
        $court = $courtModel->findById($booking['court_id']);
        
        // Format dates (Hungarian format)
        $startDate = new DateTime($booking['start_datetime']);
        $endDate = new DateTime($booking['end_datetime']);
        // Format: 2025. november 14. 09:00
        $monthNames = ['január', 'február', 'március', 'április', 'május', 'június', 
                       'július', 'augusztus', 'szeptember', 'október', 'november', 'december'];
        $startMonth = $monthNames[(int)$startDate->format('n') - 1];
        $endMonth = $monthNames[(int)$endDate->format('n') - 1];
        $startFormatted = $startDate->format('Y. ') . $startMonth . $startDate->format(' d. H:i');
        $endFormatted = $endDate->format('Y. ') . $endMonth . $endDate->format(' d. H:i');
        
        // Generate cancellation URL
        $baseUrl = getenv('APP_URL') ?: 'http://localhost/Palyafoglalo';
        $cancelUrl = $baseUrl . '/Bakcend/api/v1/bookings/cancel/' . urlencode($cancellationToken);
        
        // Email content
        $subject = 'Pályafoglalás megerősítése - ' . ($court['name'] ?? 'Pálya');
        $message = $this->buildConfirmationEmail($booking, $court, $startFormatted, $endFormatted, $cancelUrl);
        
        // Try to send email (using PHP mail() function - configure SMTP in production)
        $headers = [
            'From: ' . (getenv('MAIL_FROM') ?: 'noreply@palyafoglalo.local'),
            'Reply-To: ' . (getenv('MAIL_REPLY_TO') ?: 'info@palyafoglalo.local'),
            'Content-Type: text/html; charset=UTF-8',
            'X-Mailer: PHP/' . phpversion()
        ];
        
        // Try to send email (using PHP mail() function - configure SMTP in production)
        $success = @mail(
            $booking['customer_email'],
            $subject,
            $message,
            implode("\r\n", $headers)
        );
        
        // Also save email to file for testing/debugging (useful in MAMP/local development)
        $this->saveEmailToFile($booking['customer_email'], $subject, $message, $success);
        
        // Log notification to database
        $this->logNotification($booking['id'], $booking['customer_email'], $subject, $message, $success);
        
        // In development, if mail() fails, we still consider it "sent" because we saved it to file
        $isDevelopment = (getenv('APP_ENV') ?: 'development') === 'development';
        $finalSuccess = $success || $isDevelopment;
        
        return [
            'success' => $finalSuccess,
            'message' => $finalSuccess 
                ? ($success ? 'Email sent successfully' : 'Email saved to file (development mode)')
                : 'Failed to send email',
            'email_file' => $isDevelopment ? $this->getEmailFilePath($booking['customer_email']) : null
        ];
    }
    
    /**
     * Build confirmation email HTML
     */
    private function buildConfirmationEmail($booking, $court, $startFormatted, $endFormatted, $cancelUrl) {
        $price = $booking['price_cents'] / 100;
        $currency = $booking['currency'] ?? 'HUF';
        
        // Calculate cancellation deadline (until start time)
        $startDate = new DateTime($booking['start_datetime']);
        $cancellationDeadline = $startDate->format('Y-m-d H:i');
        $cancellationDeadlineFormatted = $startDate->format('Y. m. d. H:i');
        
        // Calculate duration
        $endDate = new DateTime($booking['end_datetime']);
        $duration = $startDate->diff($endDate);
        $hours = $duration->h;
        $minutes = $duration->i;
        $durationText = $hours > 0 ? ($hours . ' óra ' . ($minutes > 0 ? $minutes . ' perc' : '')) : ($minutes . ' perc');
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #4F46E5; color: white; padding: 20px; text-align: center; }
        .content { background-color: #f9f9f9; padding: 20px; }
        .booking-details { background-color: white; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .detail-row { margin: 10px 0; }
        .detail-label { font-weight: bold; }
        .button { display: inline-block; padding: 12px 24px; background-color: #DC2626; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        .warning { background-color: #FEF3C7; border-left: 4px solid #F59E0B; padding: 10px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Pályafoglalás megerősítése</h1>
        </div>
        <div class="content">
            <p>Kedves ' . htmlspecialchars($booking['customer_name']) . '!</p>
            <p>Foglalását sikeresen rögzítettük. Az alábbiakban a részletek:</p>
            
            <div class="booking-details">
                <div class="detail-row">
                    <span class="detail-label">Pálya:</span> ' . htmlspecialchars($court['name'] ?? 'Ismeretlen') . '
                </div>
                <div class="detail-row">
                    <span class="detail-label">Foglalás időpontja:</span> ' . htmlspecialchars($startFormatted) . ' - ' . htmlspecialchars($endFormatted) . '
                </div>
                <div class="detail-row">
                    <span class="detail-label">Időtartam:</span> ' . htmlspecialchars($durationText) . '
                </div>
                <div class="detail-row" style="font-size: 18px; font-weight: bold; color: #4F46E5; padding-top: 10px; border-top: 2px solid #E5E7EB;">
                    <span class="detail-label">Fizetendő összeg:</span> ' . number_format($price, 0, ',', ' ') . ' ' . htmlspecialchars($currency) . '
                </div>
            </div>
            
            <div class="warning" style="background-color: #DBEAFE; border-left: 4px solid #3B82F6; padding: 15px; margin: 20px 0;">
                <strong style="color: #1E40AF;">📅 Lemondási határidő:</strong><br>
                <span style="font-size: 16px; font-weight: bold; color: #1E40AF;">' . htmlspecialchars($cancellationDeadlineFormatted) . '</span><br>
                <span style="font-size: 14px; color: #1E3A8A;">A foglalást legkésőbb a kezdési időpontig lehet lemondani. Az időpont elmúlása után nem lehetséges a lemondás és a visszatérítés.</span>
            </div>
            
            <p>Ha le szeretné mondani a foglalást, kattintson az alábbi gombra:</p>
            <div style="text-align: center;">
                <a href="' . htmlspecialchars($cancelUrl) . '" class="button">Foglalás lemondása</a>
            </div>
            
            <p style="font-size: 12px; color: #666;">
                Ha a gomb nem működik, másolja be ezt a linket a böngészőbe:<br>
                ' . htmlspecialchars($cancelUrl) . '
            </p>
        </div>
        <div class="footer">
            <p>Ez egy automatikus üzenet, kérjük ne válaszoljon erre az emailre.</p>
            <p>&copy; ' . date('Y') . ' Pályafoglaló Rendszer</p>
        </div>
    </div>
</body>
</html>';
        
        return $html;
    }
    
    /**
     * Send password reset code email
     */
    public function sendPasswordResetCode($user, $code) {
        if (empty($user['email'])) {
            return ['success' => false, 'message' => 'No email address provided'];
        }
        
        $subject = 'Jelszó visszaállítási kód';
        $message = $this->buildPasswordResetEmail($user, $code);
        
        $headers = [
            'From: ' . (getenv('MAIL_FROM') ?: 'noreply@palyafoglalo.local'),
            'Reply-To: ' . (getenv('MAIL_REPLY_TO') ?: 'info@palyafoglalo.local'),
            'Content-Type: text/html; charset=UTF-8',
            'X-Mailer: PHP/' . phpversion()
        ];
        
        $success = @mail(
            $user['email'],
            $subject,
            $message,
            implode("\r\n", $headers)
        );
        
        // Log notification
        $this->logNotification(null, $user['email'], $subject, $message, $success);
        
        return [
            'success' => $success,
            'message' => $success ? 'Email sent successfully' : 'Failed to send email'
        ];
    }
    
    /**
     * Build password reset email HTML
     */
    private function buildPasswordResetEmail($user, $code) {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #4F46E5; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { background-color: #f9f9f9; padding: 20px; }
        .code-box { background-color: white; padding: 20px; margin: 20px 0; text-align: center; border-radius: 5px; border: 2px solid #4F46E5; }
        .code { font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #4F46E5; }
        .warning { background-color: #FEF3C7; border-left: 4px solid #F59E0B; padding: 10px; margin: 15px 0; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Jelszó visszaállítás</h1>
        </div>
        <div class="content">
            <p>Kedves ' . htmlspecialchars($user['full_name']) . '!</p>
            <p>Kérésedre jelszó visszaállítási kódot küldtünk.</p>
            
            <div class="code-box">
                <p style="margin: 0 0 10px 0; color: #666;">A visszaállítási kód:</p>
                <div class="code">' . htmlspecialchars($code) . '</div>
            </div>
            
            <div class="warning">
                <strong>Fontos:</strong> Ez a kód 30 percig érvényes. Ha nem te kérted ezt a kódot, hagyd figyelmen kívül ezt az emailt.
            </div>
            
            <p>Add meg ezt a kódot a jelszó visszaállítási oldalon, majd állíts be egy új jelszót.</p>
        </div>
        <div class="footer">
            <p>Ez egy automatikus üzenet, kérjük ne válaszoljon erre az emailre.</p>
            <p>&copy; ' . date('Y') . ' Pályafoglaló Rendszer</p>
        </div>
    </div>
</body>
</html>';
        
        return $html;
    }
    
    /**
     * Save email to file (for testing/debugging in local development)
     */
    private function saveEmailToFile($to, $subject, $message, $mailSuccess) {
        try {
            $logsDir = __DIR__ . '/../logs';
            if (!is_dir($logsDir)) {
                mkdir($logsDir, 0755, true);
            }
            
            $timestamp = date('Y-m-d_H-i-s');
            $filename = $logsDir . '/email_' . $timestamp . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $to) . '.html';
            
            $fileContent = "<!-- Email saved at: " . date('Y-m-d H:i:s') . " -->\n";
            $fileContent .= "<!-- To: {$to} -->\n";
            $fileContent .= "<!-- Subject: {$subject} -->\n";
            $fileContent .= "<!-- Mail function success: " . ($mailSuccess ? 'YES' : 'NO') . " -->\n\n";
            $fileContent .= $message;
            
            file_put_contents($filename, $fileContent);
            
            error_log("📧 Email saved to file: {$filename}");
        } catch (Exception $e) {
            error_log('Failed to save email to file: ' . $e->getMessage());
        }
    }
    
    /**
     * Get email file path (helper for returning in response)
     */
    private function getEmailFilePath($email) {
        $logsDir = __DIR__ . '/../logs';
        if (!is_dir($logsDir)) {
            return null;
        }
        
        // Find most recent email file for this address
        $files = glob($logsDir . '/email_*_' . preg_replace('/[^a-zA-Z0-9]/', '_', $email) . '.html');
        if (empty($files)) {
            return null;
        }
        
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        return basename($files[0]);
    }
    
    /**
     * Log notification to database
     */
    private function logNotification($bookingId, $email, $subject, $message, $success) {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO notifications (booking_id, recipient_email, notification_type, subject, message, status, sent_at) 
                 VALUES (?, ?, 'email', ?, ?, ?, ?)"
            );
            $status = $success ? 'sent' : 'failed';
            $sentAt = $success ? date('Y-m-d H:i:s') : null;
            
            $stmt->execute([
                $bookingId,
                $email,
                $subject,
                $message,
                $status,
                $sentAt
            ]);
        } catch (Exception $e) {
            error_log('Failed to log notification: ' . $e->getMessage());
        }
    }
}

