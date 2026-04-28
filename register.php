<!DOCTYPE html>
<html>
<head>
    <title>Register - Prince College</title>
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
        }
        h1 { color: #0d1b3e; }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background: #0d1b3e;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        .message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            display: none;
        }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .login-link { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create Account</h1>
        <form id="registerForm">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="text" name="student_id" placeholder="Student ID (e.g., 2024-00123)" required>
            <input type="email" name="email" placeholder="Email" required>
            <select name="role">
                <option value="student">Student</option>
                <option value="faculty">Faculty</option>
                <option value="admin">Admin</option>
            </select>
            <input type="text" name="department" placeholder="Department">
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit">Register</button>
        </form>
        <div id="message" class="message"></div>
        <div class="login-link">Already have an account? <a href="login-standalone.html">Login</a></div>
    </div>
    <script>
        document.getElementById('registerForm').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new URLSearchParams(new FormData(e.target));
            if (formData.get('password') !== formData.get('confirm_password')) {
                showMessage('Passwords do not match!', 'error');
                return;
            }
            const response = await fetch('backend/register.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                showMessage('Registration successful! Redirecting...', 'success');
                setTimeout(() => window.location.href = 'login-standalone.html', 2000);
            } else {
                showMessage(data.message, 'error');
            }
        };
        function showMessage(msg, type) {
            const m = document.getElementById('message');
            m.textContent = msg;
            m.className = 'message ' + type;
            m.style.display = 'block';
        }
    </script>
</body>
</html>