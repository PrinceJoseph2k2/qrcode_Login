<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/SecurityHelper.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/AuthSession.php';

class AuthController {
    private $db;
    private $userModel;
    private $authSessionModel;

    public function __construct() {
        $this->db = getDB();
        $this->userModel = new User($this->db);
        $this->authSessionModel = new AuthSession($this->db);
    }

    public function login() {
        session_start();
        
        $studentId = $_POST['student_id'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'student';
        $remember = isset($_POST['remember']);
        
        if (empty($studentId) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Please enter both ID and password']);
            return;
        }
        
        // Validate format based on role
        if (!SecurityHelper::validateStudentId($studentId, $role)) {
            echo json_encode(['success' => false, 'message' => 'Invalid ID format for selected role']);
            return;
        }
        
        $user = $this->userModel->findByStudentId($studentId);
        
        if (!$user || $user['role'] !== $role) {
            $this->userModel->recordLoginLog(null, 'password', 'failed', 
                SecurityHelper::getClientIP(), SecurityHelper::getUserAgent(), "Invalid credentials for role: $role");
            echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
            return;
        }
        
        if (!$this->userModel->verifyPassword($user, $password)) {
            $this->userModel->recordLoginLog($user['id'], 'password', 'failed', 
                SecurityHelper::getClientIP(), SecurityHelper::getUserAgent(), "Incorrect password");
            echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
            return;
        }
        
        // Login success
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['student_id'] = $user['student_id'];
        
        // Remember me - create persistent session
        if ($remember) {
            $sessionToken = SecurityHelper::generateSessionKey();
            $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));
            $this->authSessionModel->create($user['id'], $sessionToken, $expiresAt, SecurityHelper::getClientIP());
            setcookie('remember_token', $sessionToken, time() + (86400 * 30), '/', '', false, true);
        }
        
        $this->userModel->recordLoginLog($user['id'], 'password', 'success', 
            SecurityHelper::getClientIP(), SecurityHelper::getUserAgent(), "Login successful");
        
        echo json_encode([
            'success' => true, 
            'message' => 'Login successful',
            'redirect' => '../dashboard.php',
            'user' => [
                'name' => $user['full_name'],
                'role' => $user['role'],
                'student_id' => $user['student_id']
            ]
        ]);
    }
    
    public function logout() {
        session_start();
        
        if (isset($_COOKIE['remember_token'])) {
            $this->authSessionModel->findByToken($_COOKIE['remember_token']);
            setcookie('remember_token', '', time() - 3600, '/');
        }
        
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
    }
    
    public function verifySession() {
        session_start();
        
        if (isset($_SESSION['user_id'])) {
            echo json_encode(['success' => true, 'authenticated' => true, 'user' => [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'],
                'role' => $_SESSION['user_role']
            ]]);
            return;
        }
        
        // Check remember me cookie
        if (isset($_COOKIE['remember_token'])) {
            $session = $this->authSessionModel->findByToken($_COOKIE['remember_token']);
            if ($session && $session['expires_at'] > date('Y-m-d H:i:s')) {
                $user = $this->userModel->findById($session['user_id']);
                if ($user) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['full_name'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['student_id'] = $user['student_id'];
                    
                    echo json_encode(['success' => true, 'authenticated' => true, 'user' => [
                        'id' => $user['id'],
                        'name' => $user['full_name'],
                        'role' => $user['role']
                    ]]);
                    return;
                }
            }
        }
        
        echo json_encode(['success' => true, 'authenticated' => false]);
    }
}
?>