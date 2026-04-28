<?php
require_once __DIR__ . '/../config/database.php';

class QRHelper {
    
    public static function generateQRCode($userId, $studentId) {
        // Create qrcodes directory if it doesn't exist
        $qrDir = __DIR__ . '/../../assets/qrcodes/';
        if (!file_exists($qrDir)) {
            mkdir($qrDir, 0777, true);
        }
        
        // Generate unique secret for QR authentication
        $qrSecret = bin2hex(random_bytes(32));
        
        // Create QR code data (URL that will be used for scanning)
        $qrData = json_encode([
            'user_id' => $userId,
            'student_id' => $studentId,
            'secret' => $qrSecret,
            'timestamp' => time()
        ]);
        
        // Generate QR code using Google Charts API (simple solution)
        $qrCodeUrl = 'https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl=' . urlencode($qrData);
        
        // Download and save QR code
        $qrImageContent = file_get_contents($qrCodeUrl);
        $qrFileName = 'qr_' . $userId . '_' . time() . '.png';
        $qrFilePath = $qrDir . $qrFileName;
        
        file_put_contents($qrFilePath, $qrImageContent);
        
        // Return relative path and secret
        return [
            'path' => 'assets/qrcodes/' . $qrFileName,
            'secret' => $qrSecret
        ];
    }
    
    public static function validateQRCode($qrData) {
        $data = json_decode($qrData, true);
        if (!$data || !isset($data['user_id']) || !isset($data['secret'])) {
            return false;
        }
        
        $db = getDB();
        $stmt = $db->prepare("SELECT id, student_id, qr_secret FROM users WHERE id = :user_id AND qr_secret = :secret AND is_active = 1");
        $stmt->execute([':user_id' => $data['user_id'], ':secret' => $data['secret']]);
        
        return $stmt->fetch();
    }
}
?>