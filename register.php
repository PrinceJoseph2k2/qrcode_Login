<?php
session_start();
// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Register - Meridian College Portal</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Outfit:wght@300;400;500&display=swap" rel="stylesheet" />
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        min-height: 100vh;
        background: linear-gradient(135deg, #0d1b3e 0%, #1e2f5c 100%);
        font-family: 'Outfit', sans-serif;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }
    
    .register-container {
        max-width: 500px;
        width: 100%;
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        overflow: hidden;
        animation: slideUp 0.5s ease;
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .register-header {
        background: #0d1b3e;
        color: white;
        padding: 2rem;
        text-align: center;
    }
    
    .register-header h1 {
        font-family: 'Playfair Display', serif;
        font-size: 28px;
        margin-bottom: 0.5rem;
    }
    
    .register-header p {
        color: rgba(255,255,255,0.7);
        font-size: 14px;
    }
    
    .register-body {
        padding: 2rem;
    }
    
    .form-group {
        margin-bottom: 1.25rem;
    }
    
    .form-group label {
        display: block;
        font-size: 12px;
        font-weight: 500;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #6b7a99;
        margin-bottom: 0.5rem;
    }
    
    .form-group input, .form-group select {
        width: 100%;
        height: 46px;
        padding: 0 14px;
        border: 1.5px solid #ede9df;
        border-radius: 8px;
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        transition: all 0.2s;
    }
    
    .form-group input:focus, .form-group select:focus {
        outline: none;
        border-color: #c9a84c;
    }
    
    .register-btn {
        width: 100%;
        height: 48px;
        background: #0d1b3e;
        color: white;
        border: none;
        border-radius: 8px;
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s;
        margin-top: 1rem;
    }
    
    .register-btn:hover {
        background: #1e2f5c;
    }
    
    .login-link {
        text-align: center;
        margin-top: 1.5rem;
        font-size: 13px;
        color: #6b7a99;
    }
    
    .login-link a {
        color: #c9a84c;
        text-decoration: none;
        font-weight: 500;
    }
    
    .toast {
        position: fixed;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%) translateY(20px);
        background: #0d1b3e;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 13px;
        opacity: 0;
        transition: all 0.3s;
        pointer-events: none;
        z-index: 100;
    }
    
    .toast.show {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
</style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>Create Account</h1>
            <p>Join Meridian College Portal</p>
        </div>
        
        <div class="register-body">
            <form id="registerForm">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" required placeholder="e.g., Juan Dela Cruz" />
                </div>
                
                <div class="form-group">
                    <label>Student ID</label>
                    <input type="text" name="student_id" required placeholder="e.g., 2024-00123" />
                    <small style="color: #6b7a99; font-size: 11px;">Format: YYYY-XXXXX (e.g., 2024-00123)</small>
                </div>
                
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="e.g., juan@meridian.edu" />
                </div>
                
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" required>
                        <option value="student">Student</option>
                        <option value="faculty">Faculty</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Department</label>
                    <input type="text" name="department" placeholder="e.g., Computer Science" />
                </div>
                
                <div class="form-group">
                    <label>Year Level (Students only)</label>
                    <select name="year_level">
                        <option value="">Select Year</option>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Create a password" />
                </div>
                
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required placeholder="Confirm your password" />
                </div>
                
                <button type="submit" class="register-btn">Register & Generate QR Code</button>
            </form>
            
            <div class="login-link">
                Already have an account? <a href="index.php">Login here</a>
            </div>
        </div>
    </div>
    
    <div class="toast" id="toast"></div>
    
    <script>
        function showToast(msg, isError = false) {
            const t = document.getElementById('toast');
            t.textContent = msg;
            t.style.background = isError ? '#c0392b' : '#0d1b3e';
            t.classList.add('show');
            setTimeout(() => {
                t.classList.remove('show');
                t.style.background = '#0d1b3e';
            }, 3000);
        }
        
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const password = formData.get('password');
            const confirmPassword = formData.get('confirm_password');
            
            if (password !== confirmPassword) {
                showToast('Passwords do not match!', true);
                return;
            }
            
            const btn = e.target.querySelector('.register-btn');
            btn.disabled = true;
            btn.textContent = 'Registering...';
            
            try {
                const response = await fetch('backend/api.php?action=register', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast('Registration successful! Redirecting to login...');
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 2000);
                } else {
                    showToast(data.message || 'Registration failed', true);
                    btn.disabled = false;
                    btn.textContent = 'Register & Generate QR Code';
                }
            } catch (error) {
                showToast('An error occurred. Please try again.', true);
                btn.disabled = false;
                btn.textContent = 'Register & Generate QR Code';
            }
        });
    </script>
</body>
</html>