<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Meridian College</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #0d1b3e;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        }
        h1 {
            color: #0d1b3e;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            background: #0d1b3e;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        button:hover {
            background: #1e2f5c;
        }
        button:disabled {
            background: #6b7a99;
            cursor: not-allowed;
        }
        .message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
            display: none;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        .debug-info {
            background: #f4f4f4;
            padding: 10px;
            margin-top: 20px;
            font-size: 12px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Register for Meridian College Portal</h1>
        
        <form id="registerForm">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="full_name" id="full_name" required>
            </div>
            
            <div class="form-group">
                <label>Student ID *</label>
                <input type="text" name="student_id" id="student_id" required placeholder="e.g., 2024-00123">
            </div>
            
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" id="email" required>
            </div>
            
            <div class="form-group">
                <label>Role *</label>
                <select name="role" id="role">
                    <option value="student">Student</option>
                    <option value="faculty">Faculty</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Department</label>
                <input type="text" name="department" id="department">
            </div>
            
            <div class="form-group">
                <label>Year Level (Students only)</label>
                <select name="year_level" id="year_level">
                    <option value="">Select Year</option>
                    <option value="1">1st Year</option>
                    <option value="2">2nd Year</option>
                    <option value="3">3rd Year</option>
                    <option value="4">4th Year</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="password" id="password" required>
            </div>
            
            <div class="form-group">
                <label>Confirm Password *</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            
            <button type="submit">Register</button>
        </form>
        
        <div class="message" id="message"></div>
        
        <div class="login-link">
            Already have an account? <a href="index.php">Login here</a>
        </div>
        
        <div class="debug-info" id="debugInfo">
            <strong>Debug Info:</strong>
            <pre id="debugContent"></pre>
        </div>
    </div>
    
    <script>
        // Show/hide year level based on role
        document.getElementById('role').addEventListener('change', function() {
            const yearLevelGroup = document.getElementById('year_level').parentElement;
            if (this.value === 'student') {
                yearLevelGroup.style.display = 'block';
            } else {
                yearLevelGroup.style.display = 'none';
                document.getElementById('year_level').value = '';
            }
        });
        
        function showMessage(msg, type) {
            const msgDiv = document.getElementById('message');
            msgDiv.textContent = msg;
            msgDiv.className = 'message ' + type;
            msgDiv.style.display = 'block';
            
            setTimeout(() => {
                msgDiv.style.display = 'none';
            }, 5000);
        }
        
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                showMessage('Passwords do not match!', 'error');
                return;
            }
            
            // Collect form data
            const formData = new FormData(e.target);
            
            // Convert to URLSearchParams for proper encoding
            const params = new URLSearchParams();
            for (let pair of formData.entries()) {
                if (pair[1]) { // Only add if value is not empty
                    params.append(pair[0], pair[1]);
                }
            }
            
            const btn = document.querySelector('button');
            btn.disabled = true;
            btn.textContent = 'Registering...';
            
            // Show debug info
            const debugDiv = document.getElementById('debugInfo');
            const debugContent = document.getElementById('debugContent');
            debugContent.textContent = 'Sending data: ' + params.toString();
            debugDiv.style.display = 'block';
            
            try {
                const url = 'backend/api.php?action=register';
                console.log('Sending to:', url);
                console.log('Data:', params.toString());
                
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: params.toString()
                });
                
                const responseText = await response.text();
                console.log('Raw response:', responseText);
                
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    debugContent.textContent += '\n\nError parsing JSON: ' + e.message;
                    debugContent.textContent += '\nRaw response: ' + responseText;
                    showMessage('Server returned invalid response. Check debug info.', 'error');
                    btn.disabled = false;
                    btn.textContent = 'Register';
                    return;
                }
                
                debugContent.textContent += '\n\nResponse: ' + JSON.stringify(data, null, 2);
                
                if (data.success) {
                    showMessage('Registration successful! Redirecting to login...', 'success');
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 2000);
                } else {
                    showMessage(data.message || 'Registration failed', 'error');
                    btn.disabled = false;
                    btn.textContent = 'Register';
                }
            } catch (error) {
                console.error('Error:', error);
                debugContent.textContent += '\n\nError: ' + error.message;
                showMessage('Error: ' + error.message + '. Check console and debug info.', 'error');
                btn.disabled = false;
                btn.textContent = 'Register';
            }
        });
        
        // Trigger role change on load
        document.getElementById('role').dispatchEvent(new Event('change'));
    </script>
</body>
</html>