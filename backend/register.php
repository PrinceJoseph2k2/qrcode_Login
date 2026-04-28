<?php
// File: backend/register.php - Working registration API
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
$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$student_id = isset($_POST['student_id']) ? trim($_POST['student_id']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$role = isset($_POST['role']) ? $_POST['role'] : 'student';
$department = isset($_POST['department']) ? trim($_POST['department']) : '';
$year_level = isset($_POST['year_level']) ? $_POST['year_level'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validate required fields
if (empty($full_name) || empty($student_id) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
    exit;
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

// Validate student ID format
if (!preg_match('/^\d{4}-\d{5}$/', $student_id) && $role === 'student') {
    echo json_encode(['success' => false, 'message' => 'Student ID must be in format: YYYY-XXXXX (e.g., 2024-00123)']);
    exit;
}

try {
    // Check if student_id exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE student_id = ?");
    $stmt->execute([$student_id]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Student ID already exists']);
        exit;
    }
    
    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        exit;
    }
    
    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $sql = "INSERT INTO users (full_name, student_id, email, password_hash, role, department, year_level, is_active, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        $full_name, 
        $student_id, 
        $email, 
        $password_hash, 
        $role, 
        $department ?: null, 
        ($role === 'student' && $year_level) ? $year_level : null
    ]);
    
    if ($result) {
        echo json_encode([
            'success' => true, 
            'message' => 'Registration successful! You can now login.'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Registration failed']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>