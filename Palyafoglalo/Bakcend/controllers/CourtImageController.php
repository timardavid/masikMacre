<?php
/**
 * Court Image Controller
 * Handles court image upload and management endpoints
 */

class CourtImageController extends BaseController {
    private $imageService;
    
    public function __construct() {
        $this->imageService = new CourtImageService();
    }
    
    /**
     * POST /api/v1/courts/:id/images
     */
    public function upload($courtId) {
        // Authentication required for upload
        $user = $this->getCurrentUser();
        if (!$user) {
            $this->error('Authentication required', ['Please login to upload images'], 401);
        }
        
        // Check if file was uploaded
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $this->error('No file uploaded', ['Please select an image file'], 400);
        }
        
        $file = $_FILES['image'];
        $data = $this->getRequestBody();
        
        $result = $this->imageService->uploadImage(
            $courtId,
            $file,
            $data['alt_text'] ?? null,
            $data['display_order'] ?? null
        );
        
        if ($result['success']) {
            $this->success($result['image'], 'Image uploaded successfully', 201);
        } else {
            $this->error('Failed to upload image', $result['errors'], 400);
        }
    }
    
    /**
     * DELETE /api/v1/courts/images/:id
     */
    public function delete($imageId) {
        // Authentication required
        $user = $this->getCurrentUser();
        if (!$user) {
            $this->error('Authentication required', ['Please login to delete images'], 401);
        }
        
        $result = $this->imageService->deleteImage($imageId);
        
        if ($result['success']) {
            $this->success(null, 'Image deleted successfully');
        } else {
            $this->error('Failed to delete image', $result['errors'], 400);
        }
    }
}

