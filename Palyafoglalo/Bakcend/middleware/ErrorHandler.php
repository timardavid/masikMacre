<?php
/**
 * Error Handler Middleware
 * Handles errors and exceptions globally
 */

class ErrorHandler {
    public static function register() {
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
    }
    
    /**
     * Handle PHP errors
     */
    public static function handleError($severity, $message, $file, $line) {
        if (!(error_reporting() & $severity)) {
            return false;
        }
        
        $error = [
            'success' => false,
            'message' => 'An error occurred',
            'error' => $message
        ];
        
        if (APP_ENV === 'development') {
            $error['file'] = $file;
            $error['line'] = $line;
        }
        
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($error, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Handle exceptions
     */
    public static function handleException($exception) {
        $error = [
            'success' => false,
            'message' => 'An error occurred',
            'error' => $exception->getMessage()
        ];
        
        if (APP_ENV === 'development') {
            $error['file'] = $exception->getFile();
            $error['line'] = $exception->getLine();
            $error['trace'] = $exception->getTraceAsString();
        }
        
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($error, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Handle 404 Not Found
     */
    public static function handleNotFound() {
        http_response_code(404);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => 'Endpoint not found'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}


