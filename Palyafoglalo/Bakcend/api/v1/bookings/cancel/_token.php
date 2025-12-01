<?php
/**
 * Booking Cancellation Endpoint (via email link)
 * GET /api/v1/bookings/cancel/{token}
 */
require_once __DIR__ . '/../../../bootstrap.php';

$token = $_GET['token'] ?? null;

if (!$token) {
    http_response_code(400);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hiba</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        .error { color: #DC2626; }
    </style>
</head>
<body>
    <h1 class="error">Hiba</h1>
    <p>Érvénytelen lemondási link.</p>
    <p><a href="/">Vissza a főoldalra</a></p>
</body>
</html>';
    exit;
}

try {
    $bookingModel = new BookingModel();
    
    // Find booking by cancellation token
    $booking = $bookingModel->findByCancellationToken($token);
    
    if (!$booking) {
        http_response_code(404);
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Foglalás nem található</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        .warning { color: #F59E0B; }
    </style>
</head>
<body>
    <h1 class="warning">Foglalás nem található</h1>
    <p>A foglalás már le van mondva, lejárt, vagy érvénytelen a link.</p>
    <p><a href="/">Vissza a főoldalra</a></p>
</body>
</html>';
        exit;
    }
    
    // Check if booking has already passed
    $bookingEnd = new DateTime($booking['end_datetime']);
    $now = new DateTime();
    
    if ($bookingEnd < $now) {
        http_response_code(400);
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Foglalás lejárt</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        .error { color: #DC2626; }
    </style>
</head>
<body>
    <h1 class="error">Foglalás lejárt</h1>
    <p>Ez a foglalás már lejárt és nem mondható le.</p>
    <p><a href="/">Vissza a főoldalra</a></p>
</body>
</html>';
        exit;
    }
    
    // Cancel the booking
    $bookingService = new BookingService();
    $result = $bookingService->cancelBooking($booking['id'], null);
    
    if ($result['success']) {
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Foglalás lemondva</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        .success { color: #10B981; }
        .container { max-width: 600px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="success">✓ Foglalás sikeresen lemondva</h1>
        <p>Foglalása le lett mondva. Köszönjük, hogy értesített minket!</p>
        <p><a href="/">Vissza a főoldalra</a></p>
    </div>
</body>
</html>';
    } else {
        http_response_code(500);
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hiba</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        .error { color: #DC2626; }
    </style>
</head>
<body>
    <h1 class="error">Hiba történt</h1>
    <p>A foglalás lemondása sikertelen volt.</p>
    <p><a href="/">Vissza a főoldalra</a></p>
</body>
</html>';
    }
} catch (Exception $e) {
    error_log('Cancellation error: ' . $e->getMessage());
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hiba</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        .error { color: #DC2626; }
    </style>
</head>
<body>
    <h1 class="error">Hiba történt</h1>
    <p>Kérjük, próbálja újra később.</p>
    <p><a href="/">Vissza a főoldalra</a></p>
</body>
</html>';
}

