<?php
// ===== WEDDING WEBSITE API =====
// PHP backend API esküvői weboldalhoz
// Adatok lekérése és validálása

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// CORS preflight kérések kezelése
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Adatbázis kapcsolat beállítása
class Database {
    private $host = 'localhost';
    private $db_name = 'wedding_website';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $exception) {
            echo json_encode(['error' => 'Database connection failed: ' . $exception->getMessage()]);
            exit();
        }
        
        return $this->conn;
    }
}

// API válasz osztály
class ApiResponse {
    public static function success($data, $message = 'Success') {
        return json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE);
    }
    
    public static function error($message, $code = 400) {
        http_response_code($code);
        return json_encode([
            'status' => 'error',
            'message' => $message
        ], JSON_UNESCAPED_UNICODE);
    }
}

// Adatvalidálás osztály
class Validator {
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    public static function validatePhone($phone) {
        return preg_match('/^[\+]?[0-9\s\-\(\)]{10,20}$/', $phone);
    }
    
    public static function validateName($name) {
        return strlen(trim($name)) >= 2 && strlen(trim($name)) <= 100;
    }
    
    public static function sanitizeInput($input) {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
}

// Fő API osztály
class WeddingAPI {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Pár információk lekérése
    public function getCoupleInfo() {
        try {
            $query = "SELECT * FROM couples ORDER BY id DESC LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $couple = $stmt->fetch();
            
            if ($couple) {
                return ApiResponse::success($couple, 'Couple information retrieved successfully');
            } else {
                return ApiResponse::error('No couple information found', 404);
            }
        } catch (Exception $e) {
            return ApiResponse::error('Database error: ' . $e->getMessage(), 500);
        }
    }
    
    // Események lekérése
    public function getEvents() {
        try {
            $query = "SELECT * FROM events ORDER BY sort_order ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $events = $stmt->fetchAll();
            
            return ApiResponse::success($events, 'Events retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse::error('Database error: ' . $e->getMessage(), 500);
        }
    }
    
    // Sztori idővonal lekérése
    public function getStoryTimeline() {
        try {
            $query = "SELECT * FROM story_timeline ORDER BY sort_order ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $timeline = $stmt->fetchAll();
            
            return ApiResponse::success($timeline, 'Story timeline retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse::error('Database error: ' . $e->getMessage(), 500);
        }
    }
    
    // Galéria képek lekérése
    public function getGalleryImages() {
        try {
            $query = "SELECT * FROM gallery_images ORDER BY sort_order ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $images = $stmt->fetchAll();
            
            return ApiResponse::success($images, 'Gallery images retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse::error('Database error: ' . $e->getMessage(), 500);
        }
    }
    
    // Kapcsolattartási információk lekérése
    public function getContactInfo() {
        try {
            $query = "SELECT * FROM contact_info ORDER BY id DESC LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $contact = $stmt->fetch();
            
            if ($contact) {
                return ApiResponse::success($contact, 'Contact information retrieved successfully');
            } else {
                return ApiResponse::error('No contact information found', 404);
            }
        } catch (Exception $e) {
            return ApiResponse::error('Database error: ' . $e->getMessage(), 500);
        }
    }
    
    // Weboldal beállítások lekérése
    public function getSiteSettings() {
        try {
            $query = "SELECT setting_key, setting_value FROM site_settings";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $settings = [];
            while ($row = $stmt->fetch()) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
            
            return ApiResponse::success($settings, 'Site settings retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse::error('Database error: ' . $e->getMessage(), 500);
        }
    }
    
    // RSVP válasz mentése
    public function saveRSVP($data) {
        try {
            // Validálás
            if (!Validator::validateName($data['name'])) {
                return ApiResponse::error('Invalid name', 400);
            }
            
            if (!Validator::validateEmail($data['email'])) {
                return ApiResponse::error('Invalid email address', 400);
            }
            
            if (isset($data['phone']) && !empty($data['phone']) && !Validator::validatePhone($data['phone'])) {
                return ApiResponse::error('Invalid phone number', 400);
            }
            
            if (!in_array($data['attendance'], ['yes', 'no'])) {
                return ApiResponse::error('Invalid attendance value', 400);
            }
            
            // Adatok tisztítása
            $name = Validator::sanitizeInput($data['name']);
            $email = Validator::sanitizeInput($data['email']);
            $phone = isset($data['phone']) ? Validator::sanitizeInput($data['phone']) : null;
            $attendance = $data['attendance'];
            $guest_count = isset($data['guests']) ? (int)$data['guests'] : 1;
            $message = isset($data['message']) ? Validator::sanitizeInput($data['message']) : null;
            
            // Duplikáció ellenőrzése
            $checkQuery = "SELECT id FROM rsvp_responses WHERE email = ?";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->execute([$email]);
            
            if ($checkStmt->fetch()) {
                return ApiResponse::error('RSVP already submitted with this email address', 409);
            }
            
            // RSVP mentése
            $query = "INSERT INTO rsvp_responses (name, email, phone, attendance, guest_count, message) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([$name, $email, $phone, $attendance, $guest_count, $message]);
            
            if ($result) {
                return ApiResponse::success(['id' => $this->db->lastInsertId()], 'RSVP submitted successfully');
            } else {
                return ApiResponse::error('Failed to save RSVP', 500);
            }
        } catch (Exception $e) {
            return ApiResponse::error('Database error: ' . $e->getMessage(), 500);
        }
    }
    
    // Összes adat lekérése egy kérésben
    public function getAllData() {
        try {
            $data = [];
            
            // Pár információk
            $coupleQuery = "SELECT * FROM couples ORDER BY id DESC LIMIT 1";
            $coupleStmt = $this->db->prepare($coupleQuery);
            $coupleStmt->execute();
            $data['couple'] = $coupleStmt->fetch();
            
            // Események
            $eventsQuery = "SELECT * FROM events ORDER BY sort_order ASC";
            $eventsStmt = $this->db->prepare($eventsQuery);
            $eventsStmt->execute();
            $data['events'] = $eventsStmt->fetchAll();
            
            // Sztori idővonal
            $storyQuery = "SELECT * FROM story_timeline ORDER BY sort_order ASC";
            $storyStmt = $this->db->prepare($storyQuery);
            $storyStmt->execute();
            $data['story'] = $storyStmt->fetchAll();
            
            // Galéria képek
            $galleryQuery = "SELECT * FROM gallery_images ORDER BY sort_order ASC";
            $galleryStmt = $this->db->prepare($galleryQuery);
            $galleryStmt->execute();
            $data['gallery'] = $galleryStmt->fetchAll();
            
            // Kapcsolattartási információk
            $contactQuery = "SELECT * FROM contact_info ORDER BY id DESC LIMIT 1";
            $contactStmt = $this->db->prepare($contactQuery);
            $contactStmt->execute();
            $data['contact'] = $contactStmt->fetch();
            
            // Weboldal beállítások
            $settingsQuery = "SELECT setting_key, setting_value FROM site_settings";
            $settingsStmt = $this->db->prepare($settingsQuery);
            $settingsStmt->execute();
            $settings = [];
            while ($row = $settingsStmt->fetch()) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
            $data['settings'] = $settings;
            
            return ApiResponse::success($data, 'All data retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse::error('Database error: ' . $e->getMessage(), 500);
        }
    }
}

// API végpontok kezelése
$api = new WeddingAPI();
$method = $_SERVER['REQUEST_METHOD'];
$request = $_SERVER['REQUEST_URI'];

// URL útvonalak
$path = parse_url($request, PHP_URL_PATH);
$path = str_replace('/api/', '', $path);

switch ($path) {
    case 'couple':
        if ($method === 'GET') {
            echo $api->getCoupleInfo();
        } else {
            echo ApiResponse::error('Method not allowed', 405);
        }
        break;
        
    case 'events':
        if ($method === 'GET') {
            echo $api->getEvents();
        } else {
            echo ApiResponse::error('Method not allowed', 405);
        }
        break;
        
    case 'story':
        if ($method === 'GET') {
            echo $api->getStoryTimeline();
        } else {
            echo ApiResponse::error('Method not allowed', 405);
        }
        break;
        
    case 'gallery':
        if ($method === 'GET') {
            echo $api->getGalleryImages();
        } else {
            echo ApiResponse::error('Method not allowed', 405);
        }
        break;
        
    case 'contact':
        if ($method === 'GET') {
            echo $api->getContactInfo();
        } else {
            echo ApiResponse::error('Method not allowed', 405);
        }
        break;
        
    case 'settings':
        if ($method === 'GET') {
            echo $api->getSiteSettings();
        } else {
            echo ApiResponse::error('Method not allowed', 405);
        }
        break;
        
    case 'rsvp':
        if ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            if ($input) {
                echo $api->saveRSVP($input);
            } else {
                echo ApiResponse::error('Invalid JSON data', 400);
            }
        } else {
            echo ApiResponse::error('Method not allowed', 405);
        }
        break;
        
    case 'all':
        if ($method === 'GET') {
            echo $api->getAllData();
        } else {
            echo ApiResponse::error('Method not allowed', 405);
        }
        break;
        
    default:
        echo ApiResponse::error('Endpoint not found', 404);
        break;
}
?>
