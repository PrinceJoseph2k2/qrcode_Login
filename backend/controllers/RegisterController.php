<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/SecurityHelper.php';
require_once __DIR__ . '/../helpers/QRHelper.php';

class RegisterController {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function register() {
        // Get form data
        $fullName = $_POST['full_name'] ?? '';
        $studentId = $_POST['student_id'] ?? '';
        $email = $_POST['email'] ?? '';
        $role = $_POST['role'] ?? 'student';
        $department = $_POST['department'] ?? null;
        $yearLevel = $_POST['year_level'] ?? null;
        $password = $_POST['password'] ?? '';
        
        // Validate required fields
        if (empty($fullName) || empty($studentId) || empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
            return;
        }
        
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email address']);
            return;
        }
        
        // Check if student ID already exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE student_id = :student_id");
        $stmt->execute([':student_id' => $studentId]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Student ID already exists']);
            return;
        }
        
        // Check if email already exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Email already registered']);
            return;
        }
        
        // Hash password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user
        $stmt = $this->db->prepare("
            INSERT INTO users (full_name, student_id, email, password_hash, role, department, year_level, is_active) 
            VALUES (:full_name, :student_id, :email, :password_hash, :role, :department, :year_level, 1)
        ");
        
        $result = $stmt->execute([
            ':full_name' => $fullName,
            ':student_id' => $studentId,
            ':email' => $email,
            ':password_hash' => $passwordHash,
            ':role' => $role,
            ':department' => $department,
            ':year_level' => $role === 'student' ? $yearLevel : null
        ]);
        
        if (!$result) {
            echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
            return;
        }
        
        // Get the new user ID
        $userId = $this->db->lastInsertId();
        
        // Generate QR code for the user
        $qrData = QRHelper::generateQRCode($userId, $studentId);
        
        // Update user with QR code info
        $stmt = $this->db->prepare("UPDATE users SET qr_code = :qr_code, qr_secret = :qr_secret WHERE id = :user_id");
        $stmt->execute([
            ':qr_code' => $qrData['path'],
            ':qr_secret' => $qrData['secret'],
            ':user_id' => $userId
        ]);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Registration successful! Your QR code has been generated.',
            'user_id' => $userId
        ]);
    }
}
?>