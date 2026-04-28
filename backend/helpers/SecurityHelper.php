<?php
class SecurityHelper {
    
    public static function generateSessionKey() {
        return bin2hex(random_bytes(32));
    }
    
    public static function getClientIP() {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ips[0]);
        }
        return $ip;
    }
    
    public static function getUserAgent() {
        return $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    }
    
    public static function validateStudentId($id, $role) {
        if ($role === 'student') {
            return preg_match('/^\d{4}-\d{5}$/', $id);
        } elseif ($role === 'faculty') {
            return preg_match('/^FAC-\d{4}$/', $id);
        } elseif ($role === 'admin') {
            return preg_match('/^admin\./', $id);
        }
        return false;
    }
}
?>