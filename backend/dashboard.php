<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header('Location: login-standalone.html');
    exit();
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
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .dashboard-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .user-badge {
            background: rgba(255,255,255,0.1);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
        }
        
        .logout-btn {
            background: #c9a84c;
            color: #0d1b3e;
            padding: 0.5rem 1.5rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            transition: background 0.2s;
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
            border-radius: 16px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-left: 4px solid #c9a84c;
        }
        
        .welcome-card h2 {
            color: #0d1b3e;
            margin-bottom: 0.5rem;
        }
        
        .welcome-card p {
            color: #6b7a99;
            margin-bottom: 0.25rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
        }
        
        .stat-card h3 {
            color: #0d1b3e;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 600;
            color: #c9a84c;
            margin-top: 0.5rem;
        }
        
        .stat-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .recent-section {
            background: white;
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .recent-section h3 {
            color: #0d1b3e;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #ede9df;
        }
        
        .activity-list {
            list-style: none;
        }
        
        .activity-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid #ede9df;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-text {
            color: #333;
        }
        
        .activity-time {
            color: #6b7a99;
            font-size: 0.8rem;
        }
        
        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                text-align: center;
            }
            .dashboard-content {
                padding: 1rem;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-header">
        <h1>🎓 Meridian College Portal</h1>
        <div class="user-info">
            <div class="user-badge">👋 <?php echo htmlspecialchars($userName); ?></div>
            <div class="user-badge">🆔 <?php echo htmlspecialchars($studentId); ?></div>
            <div class="user-badge">📌 <?php echo ucfirst(htmlspecialchars($userRole)); ?></div>
            <a href="logout-standalone.php" class="logout-btn">🚪 Logout</a>
        </div>
    </div>
    
    <div class="dashboard-content">
        <div class="welcome-card">
            <h2>Welcome back, <?php echo htmlspecialchars($userName); ?>! 👋</h2>
            <p>We hope you're having a great day. Here's what's happening with your academic journey.</p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <h3>Current Courses</h3>
                <div class="stat-value">4</div>
                <p style="color: #6b7a99; font-size: 0.85rem; margin-top: 0.5rem;">This semester</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <h3>Pending Assignments</h3>
                <div class="stat-value">2</div>
                <p style="color: #6b7a99; font-size: 0.85rem; margin-top: 0.5rem;">Due this week</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <h3>Overall Grade</h3>
                <div class="stat-value">88.5%</div>
                <p style="color: #6b7a99; font-size: 0.85rem; margin-top: 0.5rem;">Average</p>
            </div>
        </div>
        
        <div class="recent-section">
            <h3>📋 Recent Activity</h3>
            <ul class="activity-list">
                <li class="activity-item">
                    <span class="activity-text">✅ You logged into the portal</span>
                    <span class="activity-time">Just now</span>
                </li>
                <li class="activity-item">
                    <span class="activity-text">📝 Assignment submitted successfully</span>
                    <span class="activity-time">Yesterday</span>
                </li>
                <li class="activity-item">
                    <span class="activity-text">📅 New schedule available</span>
                    <span class="activity-time">2 days ago</span>
                </li>
            </ul>
        </div>
    </div>
</body>
</html>