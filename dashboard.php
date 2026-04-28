<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Check for remember me cookie
    if (isset($_COOKIE['remember_token'])) {
        require_once 'backend/config/database.php';
        require_once 'backend/models/AuthSession.php';
        require_once 'backend/models/User.php';
        
        $db = getDB();
        $authSessionModel = new AuthSession($db);
        $userModel = new User($db);
        
        $session = $authSessionModel->findByToken($_COOKIE['remember_token']);
        if ($session && $session['expires_at'] > date('Y-m-d H:i:s')) {
            $user = $userModel->findById($session['user_id']);
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['student_id'] = $user['student_id'];
            }
        }
    }
    
    // If still not logged in, redirect to login
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php');
        exit();
    }
}

$userName = $_SESSION['user_name'];
$userRole = $_SESSION['user_role'];
$studentId = $_SESSION['student_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Meridian College Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Outfit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Outfit', sans-serif;
            background: #f7f4ee;
            min-height: 100vh;
        }
        
        .dashboard-header {
            background: #0d1b3e;
            color: white;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .dashboard-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .logout-btn {
            background: #c9a84c;
            color: #0d1b3e;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
        }
        
        .logout-btn:hover {
            background: #e8c870;
        }
        
        .dashboard-content {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .welcome-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <div class="dashboard-header">
        <h1>Meridian College Portal</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($userName); ?> (<?php echo htmlspecialchars($userRole); ?>)</span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
    
    <div class="dashboard-content">
        <div class="welcome-card">
            <h2>Welcome back, <?php echo htmlspecialchars($userName); ?>!</h2>
            <p>Student ID: <?php echo htmlspecialchars($studentId); ?></p>
            <p>Role: <?php echo ucfirst(htmlspecialchars($userRole)); ?></p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>📚 Current Courses</h3>
                <p style="font-size: 2rem; margin-top: 0.5rem;">4</p>
            </div>
            <div class="stat-card">
                <h3>✅ Pending Assignments</h3>
                <p style="font-size: 2rem; margin-top: 0.5rem;">2</p>
            </div>
            <div class="stat-card">
                <h3>📊 Overall Grade</h3>
                <p style="font-size: 2rem; margin-top: 0.5rem;">88.5%</p>
            </div>
        </div>
    </div>
</body>
</html>

<div class="stats-grid">
    <div class="stat-card">
        <h3>📱 My QR Code</h3>
        <p style="margin-top: 0.5rem;">
            <a href="my-qrcode.php" style="color: #c9a84c; text-decoration: none;">View/Download QR Code</a>
        </p>
    </div>
    <!-- Other stat cards -->
</div>