<?php
// File: backend/login-api.php - Standalone login API
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

// Database configuration
$host = 'localhost';
$dbname = 'meridian_college';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Only POST requests are allowed']);
    exit;
}

// Get form data
$student_id = isset($_POST['student_id']) ? trim($_POST['student_id']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$role = isset($_POST['role']) ? $_POST['role'] : 'student';

// Validate
if (empty($student_id) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please enter both ID and password']);
    exit;
}

try {
    // Find user by student_id
    $stmt = $pdo->prepare("SELECT * FROM users WHERE student_id = :student_id AND is_active = 1");
    $stmt->execute([':student_id' => $student_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        exit;
    }
    
    // Check role matches
    if ($user['role'] !== $role) {
        echo json_encode(['success' => false, 'message' => 'Invalid credentials for selected role']);
        exit;
    }
    
    // Verify password
    if (!password_verify($password, $user['password_hash'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        exit;
    }
    
    // Start session
    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['student_id'] = $user['student_id'];
    
    // Handle "remember me"
    if (isset($_POST['remember']) && $_POST['remember'] == '1') {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
        
        // Create auth_sessions table if not exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS auth_sessions (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            session_token VARCHAR(128) NOT NULL UNIQUE,
            user_id INT UNSIGNED NOT NULL,
            ip_address VARCHAR(45) DEFAULT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            expires_at DATETIME NOT NULL
        )");
        
        $stmt = $pdo->prepare("INSERT INTO auth_sessions (session_token, user_id, ip_address, expires_at) VALUES (?, ?, ?, ?)");
        $stmt->execute([$token, $user['id'], $_SERVER['REMOTE_ADDR'], $expires]);
        setcookie('remember_token', $token, time() + (86400 * 30), '/');
    }
    
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
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>