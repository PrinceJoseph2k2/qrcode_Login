<?php
// File: backend/controllers/SimpleRegisterController.php
require_once __DIR__ . '/../config/database.php';

class RegisterController {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function register() {
        // Make sure no output before this
        if (ob_get_length()) ob_clean();
        
        header('Content-Type: application/json');
        
        // Get form data
        $fullName = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
        $studentId = isset($_POST['student_id']) ? trim($_POST['student_id']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $role = isset($_POST['role']) ? $_POST['role'] : 'student';
        $department = isset($_POST['department']) ? trim($_POST['department']) : '';
        $yearLevel = isset($_POST['year_level']) ? $_POST['year_level'] : null;
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        
        // Validate required fields
        if (empty($fullName) || empty($studentId) || empty($email) || empty($password)) {
            echo json_encode([
                'success' => false, 
                'message' => 'All required fields must be filled'
            ]);
            return;
        }
        
        // Basic email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email address']);
            return;
        }
        
        try {
            // Check if student ID exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE student_id = ?");
            $stmt->execute([$studentId]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Student ID already exists']);
                return;
            }
            
            // Check if email exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Email already registered']);
                return;
            }
            
            // Hash password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user (without QR code first)
            $sql = "INSERT INTO users (full_name, student_id, email, password_hash, role, department, year_level, is_active, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $fullName, 
                $studentId, 
                $email, 
                $passwordHash, 
                $role, 
                $department ?: null, 
                ($role === 'student' && $yearLevel) ? $yearLevel : null
            ]);
            
            if ($result) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Registration successful! You can now login.'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
            }
            
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }
}
?>