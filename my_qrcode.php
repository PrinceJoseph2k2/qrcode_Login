<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

require_once 'backend/config/database.php';
$db = getDB();

// Get user's QR code
$stmt = $db->prepare("SELECT qr_code, student_id, full_name FROM users WHERE id = :user_id");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || !$user['qr_code']) {
    // Generate QR code if not exists
    require_once 'backend/helpers/QRHelper.php';
    $qrData = QRHelper::generateQRCode($_SESSION['user_id'], $_SESSION['student_id']);
    
    $stmt = $db->prepare("UPDATE users SET qr_code = :qr_code, qr_secret = :qr_secret WHERE id = :user_id");
    $stmt->execute([
        ':qr_code' => $qrData['path'],
        ':qr_secret' => $qrData['secret'],
        ':user_id' => $_SESSION['user_id']
    ]);
    $user['qr_code'] = $qrData['path'];
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
    </style>
</head>
<body>
    <div class="qr-container">
        <h1>Your QR Code</h1>
        <p>Use this QR code for quick authentication</p>
        
        <div class="qr-code">
            <img src="<?php echo htmlspecialchars($user['qr_code']); ?>" alt="QR Code" id="qrImage">
        </div>
        
        <div class="user-info">
            <strong><?php echo htmlspecialchars($user['full_name']); ?></strong><br>
            Student ID: <?php echo htmlspecialchars($user['student_id']); ?>
        </div>
        
        <div>
            <button class="download-btn" onclick="downloadQR()">Download QR Code</button>
            <button class="back-btn" onclick="window.location.href='dashboard.php'">Back to Dashboard</button>
        </div>
        
        <p class="note">⚠️ Keep your QR code secure. Do not share it with others.</p>
    </div>
    
    <script>
        function downloadQR() {
            const img = document.getElementById('qrImage');
            const link = document.createElement('a');
            link.download = 'meridian_qrcode.png';
            link.href = img.src;
            link.click();
        }
    </script>
</body>
</html>