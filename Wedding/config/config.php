<?php
// ===== WEDDING WEBSITE CONFIGURATION =====
// Konfigurációs fájl az adatbázis kapcsolathoz

// Adatbázis beállítások
define('DB_HOST', 'localhost');
define('DB_NAME', 'wedding_website');
define('DB_USER', 'root');
define('DB_PASS', '');

// Weboldal beállítások
define('SITE_URL', 'http://localhost');
define('API_URL', SITE_URL . '/api');
define('UPLOAD_PATH', 'uploads/');
define('GALLERY_PATH', 'assets/images/gallery/');

// Biztonsági beállítások
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Email beállítások (opcionális)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('FROM_EMAIL', 'noreply@wedding.com');
define('FROM_NAME', 'Wedding Website');

// Hibakezelés
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Időzóna beállítása
date_default_timezone_set('America/New_York');

// UTF-8 kódolás biztosítása
mb_internal_encoding('UTF-8');
?>
