<?php
class AuthSession {
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->cleanupExpired();
    }

    public function create($userId, $sessionToken, $expiresAt, $ipAddress = null) {
        $stmt = $this->db->prepare("
            INSERT INTO auth_sessions (session_token, user_id, ip_address, expires_at) 
            VALUES (:token, :user_id, :ip, :expires_at)
        ");
        return $stmt->execute([
            ':token' => $sessionToken,
            ':user_id' => $userId,
            ':ip' => $ipAddress,
            ':expires_at' => $expiresAt
        ]);
    }

    public function findByToken($token) {
        $stmt = $this->db->prepare("
            SELECT * FROM auth_sessions 
            WHERE session_token = :token AND expires_at > NOW()
        ");
        $stmt->execute([':token' => $token]);
        return $stmt->fetch();
    }

    public function deleteByUserId($userId) {
        $stmt = $this->db->prepare("DELETE FROM auth_sessions WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
    }

    private function cleanupExpired() {
        $stmt = $this->db->prepare("DELETE FROM auth_sessions WHERE expires_at < NOW()");
        $stmt->execute();
    }
}
?>