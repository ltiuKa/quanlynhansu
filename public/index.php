<?php
// Define base path
define('BASE_PATH', dirname(__DIR__));

// Load configuration
require_once BASE_PATH . '/app/config/config.php';
require_once BASE_PATH . '/app/config/database.php';

// Load helper functions
require_once BASE_PATH . '/app/helpers/functions.php';

// Start session
session_start();

// Get URL parameters
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Format controller name
$controllerName = ucfirst($controller) . 'Controller';

// Check if controller exists
$controllerFile = BASE_PATH . '/app/controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    // Create controller instance
    $controllerInstance = new $controllerName();
    
    // Check if action exists
    if (method_exists($controllerInstance, $action)) {
        $controllerInstance->$action();
    } else {
        // Handle 404 error
        header("HTTP/1.0 404 Not Found");
        require_once BASE_PATH . '/app/views/errors/404.php';
    }
} else {
    // Handle 404 error
    header("HTTP/1.0 404 Not Found");
    require_once BASE_PATH . '/app/views/errors/404.php';
} 