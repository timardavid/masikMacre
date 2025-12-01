<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/Controller/HomeController.php';

$controller = new HomeController();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$request = $_SERVER['REQUEST_URI'] ?? '';

// Extract the path after the script name
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$path = str_replace($scriptName, '', $request);

// Simple routing
switch ($path) {
    case '/api/':
    case '/api/home':
        echo json_encode($controller->index());
        break;
        
    case '/api/products':
        echo json_encode($controller->getProducts());
        break;
        
    case '/api/services':
        echo json_encode($controller->getServices());
        break;
        
    case '/api/contact':
        if ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            echo json_encode($controller->sendContactEmail($input));
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;
        
    default:
        if (preg_match('/^\/api\/page\/(.+)$/', $path, $matches)) {
            $slug = $matches[1];
            $result = $controller->getPage($slug);
            if ($result) {
                echo json_encode($result);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Page not found']);
            }
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
        }
        break;
}
