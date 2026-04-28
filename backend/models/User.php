<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function findByStudentId($studentId) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE student_id = :student_id AND is_active = 1");
        $stmt->execute([':student_id' => $studentId]);
        return $stmt->fetch();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT id, student_id, full_name, email, role, department, year_level, avatar_url, is_active, created_at FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function verifyPassword($user, $password) {
        return password_verify($password, $user['password_hash']);
    }

    public function recordLoginLog($userId, $loginType, $status, $ip, $userAgent, $note = null) {
        $stmt = $this->db->prepare("
            INSERT INTO login_logs (user_id, login_type, status, ip_address, user_agent, note) 
            VALUES (:user_id, :login_type, :status, :ip, :user_agent, :note)
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':login_type' => $loginType,
            ':status' => $status,
            ':ip' => $ip,
            ':user_agent' => $userAgent,
            ':note' => $note
        ]);
    }
}
?>