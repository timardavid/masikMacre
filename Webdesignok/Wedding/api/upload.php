<?php
// ===== IMAGE UPLOAD HANDLER =====
// Képfeltöltési kezelő az esküvői weboldalhoz

require_once '../config/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// CORS preflight kérések kezelése
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Csak POST kérések engedélyezése
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Képfeltöltési osztály
class ImageUploader {
    private $uploadPath;
    private $maxFileSize;
    private $allowedTypes;
    
    public function __construct() {
        $this->uploadPath = GALLERY_PATH;
        $this->maxFileSize = MAX_FILE_SIZE;
        $this->allowedTypes = ALLOWED_IMAGE_TYPES;
        
        // Upload mappa létrehozása ha nem létezik
        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }
    
    public function uploadImage($file, $category = 'general') {
        try {
            // Validálás
            $validation = $this->validateFile($file);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'error' => $validation['error']
                ];
            }
            
            // Fájlnév generálása
            $filename = $this->generateFilename($file['name'], $category);
            $filepath = $this->uploadPath . $filename;
            
            // Fájl mozgatása
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                // Adatbázisba mentés
                $dbResult = $this->saveToDatabase($filename, $file, $category);
                
                if ($dbResult['success']) {
                    return [
                        'success' => true,
                        'filename' => $filename,
                        'url' => GALLERY_PATH . $filename,
                        'id' => $dbResult['id']
                    ];
                } else {
                    // Fájl törlése ha adatbázis mentés sikertelen
                    unlink($filepath);
                    return [
                        'success' => false,
                        'error' => 'Database save failed: ' . $dbResult['error']
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'error' => 'Failed to move uploaded file'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Upload error: ' . $e->getMessage()
            ];
        }
    }
    
    private function validateFile($file) {
        // Fájl létezés ellenőrzése
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'error' => 'No file uploaded or upload error'];
        }
        
        // Fájlméret ellenőrzése
        if ($file['size'] > $this->maxFileSize) {
            return ['valid' => false, 'error' => 'File too large. Maximum size: ' . ($this->maxFileSize / 1024 / 1024) . 'MB'];
        }
        
        // Fájltípus ellenőrzése
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $this->allowedTypes)) {
            return ['valid' => false, 'error' => 'Invalid file type. Allowed: ' . implode(', ', $this->allowedTypes)];
        }
        
        // MIME típus ellenőrzése
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowedMimes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp'
        ];
        
        if (!isset($allowedMimes[$fileExtension]) || $mimeType !== $allowedMimes[$fileExtension]) {
            return ['valid' => false, 'error' => 'Invalid MIME type'];
        }
        
        return ['valid' => true];
    }
    
    private function generateFilename($originalName, $category) {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $timestamp = date('Y-m-d_H-i-s');
        $random = substr(md5(uniqid()), 0, 8);
        
        return $category . '_' . $timestamp . '_' . $random . '.' . $extension;
    }
    
    private function saveToDatabase($filename, $file, $category) {
        try {
            $database = new Database();
            $conn = $database->getConnection();
            
            // Alt text generálása
            $altText = $this->generateAltText($filename, $category);
            
            // Title generálása
            $title = $this->generateTitle($category);
            
            // Description generálása
            $description = $this->generateDescription($category);
            
            // Sort order meghatározása
            $sortQuery = "SELECT MAX(sort_order) as max_order FROM gallery_images";
            $sortStmt = $conn->prepare($sortQuery);
            $sortStmt->execute();
            $maxOrder = $sortStmt->fetch()['max_order'] ?? 0;
            $sortOrder = $maxOrder + 1;
            
            // Beszúrás
            $query = "INSERT INTO gallery_images (filename, alt_text, category, title, description, sort_order) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $result = $stmt->execute([$filename, $altText, $category, $title, $description, $sortOrder]);
            
            if ($result) {
                return [
                    'success' => true,
                    'id' => $conn->lastInsertId()
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Failed to insert into database'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    private function generateAltText($filename, $category) {
        $categoryNames = [
            'engagement' => 'Engagement',
            'travel' => 'Travel',
            'daily' => 'Daily Life',
            'wedding' => 'Wedding',
            'general' => 'Photo'
        ];
        
        $categoryName = $categoryNames[$category] ?? 'Photo';
        return $categoryName . ' - ' . pathinfo($filename, PATHINFO_FILENAME);
    }
    
    private function generateTitle($category) {
        $titles = [
            'engagement' => 'Engagement Photo',
            'travel' => 'Travel Memory',
            'daily' => 'Daily Moment',
            'wedding' => 'Wedding Photo',
            'general' => 'Photo'
        ];
        
        return $titles[$category] ?? 'Photo';
    }
    
    private function generateDescription($category) {
        $descriptions = [
            'engagement' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'travel' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'daily' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'wedding' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'general' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
        ];
        
        return $descriptions[$category] ?? 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.';
    }
}

// Képfeltöltés kezelése
if (isset($_FILES['image']) && isset($_POST['category'])) {
    $uploader = new ImageUploader();
    $category = $_POST['category'];
    
    $result = $uploader->uploadImage($_FILES['image'], $category);
    
    if ($result['success']) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Image uploaded successfully',
            'data' => $result
        ], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => $result['error']
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'No image file or category provided'
    ], JSON_UNESCAPED_UNICODE);
}
?>
