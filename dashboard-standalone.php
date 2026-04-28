<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login-standalone.html');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Meridian College</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f4ee;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 { color: #0d1b3e; }
        .info { background: #f0f0f0; padding: 15px; border-radius: 5px; margin: 20px 0; }
        button {
            background: #c0392b;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to Dashboard</h1>
        <div class="info">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
            <p><strong>Student ID:</strong> <?php echo htmlspecialchars($_SESSION['student_id']); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars($_SESSION['user_role']); ?></p>
        </div>
        <button onclick="logout()">Logout</button>
    </div>
    
    <script>
        function logout() {
            window.location.href = 'logout-standalone.php';
        }
    </script>
</body>
</html>