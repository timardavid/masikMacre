<?php
/**
 * Simple Router
 * Handles routing for REST API
 */

class Router {
    private $routes = [];
    private $middleware = [];
    
    /**
     * Add route
     */
    public function addRoute($method, $path, $handler, $middleware = []) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }
    
    /**
     * Register middleware
     */
    public function registerMiddleware($name, $callback) {
        $this->middleware[$name] = $callback;
    }
    
    /**
     * Dispatch request
     */
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $this->getUri();
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            
            $pattern = $this->convertPathToRegex($route['path']);
            if (preg_match($pattern, $uri, $matches)) {
                // Execute middleware
                foreach ($route['middleware'] as $middlewareName) {
                    if (isset($this->middleware[$middlewareName])) {
                        call_user_func($this->middleware[$middlewareName]);
                    }
                }
                
                // Extract parameters
                array_shift($matches);
                
                // Call handler
                $handler = $route['handler'];
                if (is_string($handler) && strpos($handler, '@') !== false) {
                    [$controller, $method] = explode('@', $handler);
                    $controllerInstance = new $controller();
                    call_user_func_array([$controllerInstance, $method], $matches);
                } else if (is_callable($handler)) {
                    call_user_func_array($handler, $matches);
                }
                
                return;
            }
        }
        
        // No route matched
        ErrorHandler::handleNotFound();
    }
    
    /**
     * Get current URI
     */
    private function getUri() {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Remove query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        
        // Remove /Palyafoglalo/Bakcend prefix if present
        if (strpos($uri, '/Palyafoglalo/Bakcend') === 0) {
            $uri = substr($uri, strlen('/Palyafoglalo/Bakcend'));
        }
        
        // Remove API base path
        $basePath = API_BASE_PATH;
        if (strpos($uri, $basePath) !== false) {
            $uri = substr($uri, strpos($uri, $basePath) + strlen($basePath));
        }
        
        // Ensure starts with /
        if ($uri[0] !== '/') {
            $uri = '/' . $uri;
        }
        
        // Remove trailing slash except for root
        if (strlen($uri) > 1 && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }
        
        return $uri;
    }
    
    /**
     * Convert route path to regex pattern
     */
    private function convertPathToRegex($path) {
        $pattern = preg_replace('/\{(\w+)\}/', '([^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';
        return $pattern;
    }
}


