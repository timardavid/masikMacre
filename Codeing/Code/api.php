<?php
/**
 * Main API Endpoint
 * Handles all dynamic content requests
 */

require_once 'config/database.php';

// Set CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get request method and endpoint
$method = $_SERVER['REQUEST_METHOD'];
$request = $_GET['endpoint'] ?? '';

try {
    switch ($request) {
        case 'services':
            handleServices($method);
            break;
            
        case 'classes':
            handleClasses($method);
            break;
            
        case 'about':
            handleAbout($method);
            break;
            
        case 'contact-info':
            handleContactInfo($method);
            break;
            
        case 'site-settings':
            handleSiteSettings($method);
            break;
            
        case 'contact-form':
            handleContactForm($method);
            break;
            
        default:
            (new APIResponse(null, 404, 'Endpoint not found'))->send();
    }
} catch (Exception $e) {
    Utils::logError("API Error: " . $e->getMessage());
    (new APIResponse(null, 500, 'Internal server error'))->send();
}

/**
 * Handle services endpoint
 */
function handleServices($method) {
    if ($method !== 'GET') {
        (new APIResponse(null, 405, 'Method not allowed'))->send();
    }
    
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM services ORDER BY id ASC");
    $stmt->execute();
    $services = $stmt->fetchAll();
    
    // Decode JSON features
    foreach ($services as &$service) {
        $service['features'] = json_decode($service['features'], true);
    }
    
    (new APIResponse($services, 200, 'Services retrieved successfully'))->send();
}

/**
 * Handle classes endpoint
 */
function handleClasses($method) {
    if ($method !== 'GET') {
        (new APIResponse(null, 405, 'Method not allowed'))->send();
    }
    
    $db = Database::getInstance()->getConnection();
    $category = $_GET['category'] ?? '';
    
    if ($category) {
        $stmt = $db->prepare("SELECT * FROM classes WHERE category = ? ORDER BY id ASC");
        $stmt->execute([$category]);
    } else {
        $stmt = $db->prepare("SELECT * FROM classes ORDER BY id ASC");
        $stmt->execute();
    }
    
    $classes = $stmt->fetchAll();
    (new APIResponse($classes, 200, 'Classes retrieved successfully'))->send();
}

/**
 * Handle about content endpoint
 */
function handleAbout($method) {
    if ($method !== 'GET') {
        (new APIResponse(null, 405, 'Method not allowed'))->send();
    }
    
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM about_content ORDER BY id ASC");
    $stmt->execute();
    $about = $stmt->fetchAll();
    
    (new APIResponse($about, 200, 'About content retrieved successfully'))->send();
}

/**
 * Handle contact info endpoint
 */
function handleContactInfo($method) {
    if ($method !== 'GET') {
        (new APIResponse(null, 405, 'Method not allowed'))->send();
    }
    
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM contact_info ORDER BY id ASC");
    $stmt->execute();
    $contactInfo = $stmt->fetchAll();
    
    (new APIResponse($contactInfo, 200, 'Contact info retrieved successfully'))->send();
}

/**
 * Handle site settings endpoint
 */
function handleSiteSettings($method) {
    if ($method !== 'GET') {
        (new APIResponse(null, 405, 'Method not allowed'))->send();
    }
    
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT setting_key, setting_value FROM site_settings");
    $stmt->execute();
    $settings = $stmt->fetchAll();
    
    // Convert to associative array
    $settingsArray = [];
    foreach ($settings as $setting) {
        $settingsArray[$setting['setting_key']] = $setting['setting_value'];
    }
    
    (new APIResponse($settingsArray, 200, 'Site settings retrieved successfully'))->send();
}

/**
 * Handle contact form submission
 */
function handleContactForm($method) {
    if ($method !== 'POST') {
        (new APIResponse(null, 405, 'Method not allowed'))->send();
    }
    
    // Validate CSRF token
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!Utils::validateCSRFToken($csrfToken)) {
        (new APIResponse(null, 403, 'Invalid CSRF token'))->send();
    }
    
    // Get and validate form data
    $name = Utils::sanitizeInput($_POST['name'] ?? '');
    $email = Utils::sanitizeInput($_POST['email'] ?? '');
    $phone = Utils::sanitizeInput($_POST['phone'] ?? '');
    $service = Utils::sanitizeInput($_POST['service'] ?? '');
    $message = Utils::sanitizeInput($_POST['message'] ?? '');
    
    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($email) || !Utils::validateEmail($email)) {
        $errors[] = 'Valid email is required';
    }
    
    if (empty($message)) {
        $errors[] = 'Message is required';
    }
    
    if (!empty($errors)) {
        (new APIResponse(['errors' => $errors], 400, 'Validation failed'))->send();
    }
    
    // Save to database
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("
        INSERT INTO contact_submissions (name, email, phone, service, message) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $result = $stmt->execute([$name, $email, $phone, $service, $message]);
    
    if ($result) {
        // Send email notification (optional)
        sendEmailNotification($name, $email, $phone, $service, $message);
        
        (new APIResponse(null, 201, 'Message sent successfully'))->send();
    } else {
        (new APIResponse(null, 500, 'Failed to send message'))->send();
    }
}

/**
 * Send email notification
 */
function sendEmailNotification($name, $email, $phone, $service, $message) {
    $to = ADMIN_EMAIL;
    $subject = "New Contact Form Submission - $service";
    
    $body = "
    New contact form submission:
    
    Name: $name
    Email: $email
    Phone: $phone
    Service: $service
    
    Message:
    $message
    
    Submitted at: " . date('Y-m-d H:i:s');
    
    $headers = "From: noreply@fitnessstudio.com\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    // Uncomment to enable email sending
    // mail($to, $subject, $body, $headers);
}
?>
