<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login-standalone.html');
    exit();
}

require_once 'backend/config/database.php';
$db = getDB();

// Get user's QR code
$stmt = $db->prepare("SELECT qr_code, student_id, full_name FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// If no QR code exists, create a simple one
if (!$user['qr_code']) {
    $qrDir = __DIR__ . '/assets/qrcodes/';
    if (!file_exists($qrDir)) {
        mkdir($qrDir, 0777, true);
    }
    
    $qrData = json_encode([
        'user_id' => $_SESSION['user_id'],
        'student_id' => $_SESSION['student_id'],
        'name' => $_SESSION['user_name']
    ]);
    
    $qrCodeUrl = 'https://quickchart.io/qr?text=' . urlencode($qrData) . '&size=300';
    $qrImageContent = @file_get_contents($qrCodeUrl);
    
    if ($qrImageContent) {
        $qrFileName = 'qr_' . $_SESSION['user_id'] . '_' . time() . '.png';
        file_put_contents($qrDir . $qrFileName, $qrImageContent);
        
        $stmt = $db->prepare("UPDATE users SET qr_code = ? WHERE id = ?");
        $stmt->execute(['assets/qrcodes/' . $qrFileName, $_SESSION['user_id']]);
        $user['qr_code'] = 'assets/qrcodes/' . $qrFileName;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My QR Code - Meridian College</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Outfit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #0d1b3e 0%, #1e2f5c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .qr-container {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        .qr-container h1 {
            font-family: 'Playfair Display', serif;
            color: #0d1b3e;
            margin-bottom: 0.5rem;
        }
        
        .qr-code {
            margin: 2rem 0;
            padding: 1rem;
            background: white;
            border-radius: 10px;
        }
        
        .qr-code img {
            max-width: 250px;
            height: auto;
            border: 3px solid #c9a84c;
            border-radius: 10px;
            padding: 10px;
        }
        
        .user-info {
            background: #f7f4ee;
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
        }
        
        .download-btn {
            background: #0d1b3e;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-family: 'Outfit', sans-serif;
            font-weight: 500;
            margin-top: 1rem;
        }
        
        .back-btn {
            background: #c9a84c;
            color: #0d1b3e;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-family: 'Outfit', sans-serif;
            font-weight: 500;
            margin-left: 1rem;
        }
        
        .note {
            margin-top: 1.5rem;
            font-size: 12px;
            color: #6b7a99;
        }
        
        @media (max-width: 500px) {
            .qr-container {
                padding: 1.5rem;
            }
            .back-btn, .download-btn {
                display: block;
                width: 100%;
                margin: 0.5rem 0;
            }
        }
    </style>
</head>
<body>
    <div class="qr-container">
        <h1>📱 Your QR Code</h1>
        <p>Use this QR code for quick authentication</p>
        
        <div class="qr-code">
            <?php if ($user['qr_code'] && file_exists($user['qr_code'])): ?>
                <img src="<?php echo htmlspecialchars($user['qr_code']); ?>" alt="QR Code" id="qrImage">
            <?php else: ?>
                <div style="padding: 50px; background: #f0f0f0; border-radius: 10px;">
                    <p>QR Code not available yet.</p>
                    <p>Please contact administrator.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="user-info">
            <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong><br>
            Student ID: <?php echo htmlspecialchars($_SESSION['student_id']); ?>
        </div>
        
        <div>
            <?php if ($user['qr_code'] && file_exists($user['qr_code'])): ?>
                <button class="download-btn" onclick="downloadQR()">⬇️ Download QR Code</button>
            <?php endif; ?>
            <button class="back-btn" onclick="window.location.href='dashboard.php'">← Back to Dashboard</button>
        </div>
        
        <p class="note">⚠️ Keep your QR code secure. Do not share it with others.</p>
    </div>
    
    <script>
        function downloadQR() {
            const img = document.getElementById('qrImage');
            if (img) {
                const link = document.createElement('a');
                link.download = 'meridian_qrcode.png';
                link.href = img.src;
                link.click();
            }
        }
    </script>
</body>
</html>