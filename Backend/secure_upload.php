<?php
require_once __DIR__ . '/config.php';

class SecureFileUpload {
    private $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
    private $maxFileSize = 5 * 1024 * 1024; // 5MB
    private $uploadPath = '../uploads/';
    
    public function uploadFile($file, $subfolder = '') {
        // Validate file
        if (!$this->validateFile($file)) {
            return ['success' => false, 'error' => 'Invalid file'];
        }
        
        // Generate secure filename
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = $this->generateSecureFilename($extension);
        $fullPath = $this->uploadPath . $subfolder . $filename;
        
        // Create directory if it doesn't exist
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $fullPath)) {
            // Set proper permissions
            chmod($fullPath, 0644);
            
            return [
                'success' => true,
                'filename' => $filename,
                'path' => $subfolder . $filename
            ];
        }
        
        return ['success' => false, 'error' => 'Upload failed'];
    }
    
    private function validateFile($file) {
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return false;
        }
        
        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            return false;
        }
        
        // Check file type
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedTypes)) {
            return false;
        }
        
        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowedMimes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        if (!isset($allowedMimes[$extension]) || $mimeType !== $allowedMimes[$extension]) {
            return false;
        }
        
        return true;
    }
    
    private function generateSecureFilename($extension) {
        return bin2hex(random_bytes(16)) . '.' . $extension;
    }
}
?>
