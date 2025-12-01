<?php
/**
 * Court Image Service
 * Handles business logic for court image uploads and management
 */

class CourtImageService {
    private $imageModel;
    private $courtModel;
    
    private $uploadDir = __DIR__ . '/../uploads/courts/';
    private $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];
    private $maxFileSize = 5 * 1024 * 1024; // 5MB
    
    public function __construct() {
        $this->imageModel = new CourtImageModel();
        $this->courtModel = new CourtModel();
        
        // Create upload directory if it doesn't exist
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    /**
     * Upload image for a court
     */
    public function uploadImage($courtId, $file, $altText = null, $displayOrder = null) {
        $errors = [];
        
        // Validate court
        $court = $this->courtModel->findById($courtId);
        if (!$court) {
            return ['success' => false, 'errors' => ['Court not found']];
        }
        
        // Validate file
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['success' => false, 'errors' => ['No file uploaded']];
        }
        
        // Check file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $this->allowedTypes)) {
            return ['success' => false, 'errors' => ['Invalid file type. Allowed: JPEG, PNG, WebP, GIF']];
        }
        
        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            return ['success' => false, 'errors' => ['File size exceeds 5MB limit']];
        }
        
        try {
            // Generate unique filename
            $extension = $this->getExtensionFromMime($mimeType);
            $filename = 'court_' . $courtId . '_' . uniqid() . '_' . time() . '.' . $extension;
            $filepath = $this->uploadDir . $filename;
            
            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                return ['success' => false, 'errors' => ['Failed to save file']];
            }
            
            // Generate URL
            $imageUrl = '/Palyafoglalo/Bakcend/uploads/courts/' . $filename;
            
            // Get max display order if not provided
            if ($displayOrder === null) {
                $existingImages = $this->imageModel->findByCourtId($courtId, false);
                $displayOrder = count($existingImages);
            }
            
            // Save to database
            $imageData = [
                'court_id' => $courtId,
                'image_url' => $imageUrl,
                'image_path' => $filepath,
                'alt_text' => $altText ?: $court['name'],
                'display_order' => (int)$displayOrder,
                'is_active' => 1
            ];
            
            $imageId = $this->imageModel->create($imageData);
            $image = $this->imageModel->findById($imageId);
            
            // If this is the first image, set as main image
            if (empty($court['main_image_url'])) {
                $this->courtModel->update($courtId, ['main_image_url' => $imageUrl]);
            }
            
            return ['success' => true, 'image' => $image];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['Failed to upload image: ' . $e->getMessage()]];
        }
    }
    
    /**
     * Delete image
     */
    public function deleteImage($imageId) {
        $image = $this->imageModel->findById($imageId);
        
        if (!$image) {
            return ['success' => false, 'errors' => ['Image not found']];
        }
        
        try {
            // Delete file
            if (!empty($image['image_path']) && file_exists($image['image_path'])) {
                unlink($image['image_path']);
            }
            
            // Delete from database
            $this->imageModel->deleteById($imageId);
            
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['Failed to delete image: ' . $e->getMessage()]];
        }
    }
    
    /**
     * Get extension from MIME type
     */
    private function getExtensionFromMime($mimeType) {
        $mimeMap = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif'
        ];
        
        return $mimeMap[$mimeType] ?? 'jpg';
    }
}

