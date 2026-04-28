<?php
require_once 'config/app.php';
require_once 'controllers/AuthController.php';

$action = $_GET['action'] ?? '';
$authController = new AuthController();

switch ($action) {
    case 'login':
        $authController->login();
        break;
    case 'logout':
        $authController->logout();
        break;
    case 'verify-session':
        $authController->verifySession();
        break;
    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>