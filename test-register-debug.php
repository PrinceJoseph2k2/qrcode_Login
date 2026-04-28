<?php
// File: test-register-debug.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

echo "Testing PHP execution...\n\n";

// Check if we can connect to database
try {
    $db = new PDO('mysql:host=localhost;dbname=meridian_college', 'root', '');
    echo "✓ Database connection successful\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}

// Check if we can create directories
$qrDir = __DIR__ . '/assets/qrcodes/';
echo "QR Code directory path: " . $qrDir . "\n";

if (file_exists($qrDir)) {
    echo "✓ QR directory exists\n";
} else {
    echo "✗ QR directory does not exist, attempting to create...\n";
    if (mkdir($qrDir, 0777, true)) {
        echo "✓ QR directory created successfully\n";
    } else {
        echo "✗ Failed to create QR directory\n";
    }
}

// Check if we can write to the directory
if (is_writable($qrDir)) {
    echo "✓ QR directory is writable\n";
} else {
    echo "✗ QR directory is NOT writable\n";
}

// Test insert into database
try {
    $testId = 'TEST_' . time();
    $stmt = $db->prepare("INSERT INTO users (full_name, student_id, email, password_hash, role, is_active) 
                          VALUES (:name, :id, :email, :pass, 'student', 1)");
    $stmt->execute([
        ':name' => 'Test User',
        ':id' => $testId,
        ':email' => 'test@test.com',
        ':pass' => password_hash('test', PASSWORD_DEFAULT)
    ]);
    echo "✓ Database insert successful\n";
    
    // Clean up test entry
    $db->prepare("DELETE FROM users WHERE student_id = :id")->execute([':id' => $testId]);
    echo "✓ Test entry cleaned up\n";
} catch (Exception $e) {
    echo "✗ Database insert failed: " . $e->getMessage() . "\n";
}
?>