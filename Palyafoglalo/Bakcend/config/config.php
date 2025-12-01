<?php
/**
 * Application Configuration
 * Environment-based configuration for the Tennis Court Booking System
 */

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Europe/Budapest');

// Application constants (only define if not already defined)
if (!defined('APP_NAME')) {
    define('APP_NAME', 'Pályafoglaló');
    define('APP_VERSION', '1.0.0');
    define('APP_ENV', getenv('APP_ENV') ?: 'development'); // development, production

    // API Configuration
    define('API_VERSION', 'v1');
    define('API_BASE_PATH', '/api/v1');

    // JWT Configuration (for authentication)
    define('JWT_SECRET', getenv('JWT_SECRET') ?: 'your-secret-key-change-in-production');
    define('JWT_ALGORITHM', 'HS256');
    define('JWT_EXPIRATION', 86400 * 7); // 7 days in seconds (increased from 24 hours for better UX)

    // CORS Configuration (as constant value, not array - will use in code)
    // Note: CORS_ALLOWED_ORIGINS should be an array, but define() doesn't support arrays well
    // We'll handle this differently
}

// Database Configuration (MAMP defaults - adjust as needed)
return [
    'database' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'port' => getenv('DB_PORT') ?: '3306',
        'database' => getenv('DB_NAME') ?: 'palyafoglalo',
        'username' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASS') ?: 'root',
        'charset' => 'utf8mb4',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ]
    ],
    
    'pagination' => [
        'default_limit' => 20,
        'max_limit' => 100
    ],
    
    'booking' => [
        'timezone' => 'Europe/Budapest',
        'default_currency' => 'HUF'
    ]
];

