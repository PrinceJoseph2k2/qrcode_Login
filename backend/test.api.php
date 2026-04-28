<?php
// File: test-api.php
?>
<!DOCTYPE html>
<html>
<head>
    <title>API Test</title>
</head>
<body>
    <h1>Testing Registration API</h1>
    <button onclick="testAPI()">Test Register API</button>
    <div id="result"></div>
    
    <script>
    async function testAPI() {
        const formData = new URLSearchParams();
        formData.append('full_name', 'Test User');
        formData.append('student_id', '2024-9999');
        formData.append('email', 'test@meridian.edu');
        formData.append('role', 'student');
        formData.append('password', 'Password123!');
        formData.append('confirm_password', 'Password123!');
        
        document.getElementById('result').innerHTML = 'Sending request...';
        
        try {
            const response = await fetch('backend/api.php?action=register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData.toString()
            });
            
            const data = await response.json();
            document.getElementById('result').innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
        } catch (error) {
            document.getElementById('result').innerHTML = 'Error: ' + error.message;
        }
    }
    </script>
</body>
</html>