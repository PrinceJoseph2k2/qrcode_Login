<?php
// File: backend/api.php - CLEAN VERSION with no extra output
error_reporting(0); // Disable error reporting to avoid breaking JSON
ini_set('display_errors', 0);

// Set JSON header FIRST thing
header('Content-Type: application/json');

require_once 'config/database.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/SimpleRegisterController.php';

// Get action from GET parameter
$action = isset($_GET['action']) ? $_GET['action'] : '';

try {
    $authController = new AuthController();
    $registerController = new RegisterController();
    
    switch ($action) {
        case 'login':
            $authController->login();
            break;
        case 'register':
            $registerController->register();
            break;
        case 'logout':
            $authController->logout();
            break;
        case 'verify-session':
            $authController->verifySession();
            break;
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action: ' . $action]);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>