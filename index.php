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
<title>Student Portal — Login</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Outfit:wght@300;400;500&display=swap" rel="stylesheet" />
<link href="assets/css/style.css" rel="stylesheet" />
</head>
<body>

<div class="left-panel">
  <div>
    <div class="seal">
      <div class="seal-inner">
        <svg viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M14 3L4 8v6c0 5.25 4.27 10.16 10 11.33C19.73 24.16 24 19.25 24 14V8L14 3z" stroke="#c9a84c" stroke-width="1.2" fill="none"/>
          <path d="M10 14l3 3 5-5" stroke="#c9a84c" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
    </div>
    <div class="school-name">Prince<br/>College</div>
    <div class="school-tagline">Est. 1968 · Excellence in Education</div>
    <p class="portal-desc">Access your academic records, course enrollments, grades, library resources, and campus services — all in one secure portal.</p>
  </div>

  <div>
    <nav class="nav-links">
      <a class="nav-link"><span class="nav-link-dot"></span>Academic Calendar</a>
      <a class="nav-link"><span class="nav-link-dot"></span>Admissions Office</a>
      <a class="nav-link"><span class="nav-link-dot"></span>IT Support</a>
      <a class="nav-link"><span class="nav-link-dot"></span>Campus Map</a>
    </nav>
    <div style="margin-top: 2rem;" class="year-badge">A.Y. 2025 – 2026</div>
  </div>
</div>

<div class="right-panel">
  <div class="login-card">
    <h1 class="login-title">Welcome back</h1>
    <p class="login-subtitle">Sign in to your student portal</p>

    <div class="role-tabs">
      <button type="button" class="role-tab active" data-role="student">Student</button>
      <button type="button" class="role-tab" data-role="faculty">Faculty</button>
      <button type="button" class="role-tab" data-role="admin">Admin</button>
    </div>

    <form id="loginForm">
      <div class="field-group">
        <label class="field-label" id="idLabel">Student ID</label>
        <input class="field-input" id="idInput" name="student_id" type="text" placeholder="e.g. 2024-00123" autocomplete="username" required />
      </div>

      <div class="field-group">
        <label class="field-label">Password</label>
        <div class="field-input-wrap">
          <input class="field-input" id="pwInput" name="password" type="password" placeholder="Enter your password" autocomplete="current-password" required />
          <button type="button" class="toggle-pw" onclick="togglePw()" title="Toggle password">
            <svg id="eyeIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
            </svg>
          </button>
        </div>
      </div>

      <div class="field-row">
        <label class="remember-wrap">
          <input type="checkbox" name="remember" id="remember" />
          <span class="remember-label">Remember me</span>
        </label>
        <a href="#" class="forgot-link" onclick="showToast('Contact IT Support to reset your password.'); return false;">Forgot password?</a>
      </div>

      <input type="hidden" name="role" id="roleInput" value="student" />
      
      <button type="submit" class="login-btn" id="loginBtn">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>
        </svg>
        Sign in
      </button>
    </form>

    <div class="divider">
      <div class="divider-line"></div>
      <span class="divider-text">or continue with</span>
      <div class="divider-line"></div>
    </div>

    <button class="sso-btn" onclick="showToast('Redirecting to Microsoft SSO...')">
      <svg width="16" height="16" viewBox="0 0 21 21">
        <rect x="1" y="1" width="9" height="9" fill="#f25022"/>
        <rect x="11" y="1" width="9" height="9" fill="#7fba00"/>
        <rect x="1" y="11" width="9" height="9" fill="#00a4ef"/>
        <rect x="11" y="11" width="9" height="9" fill="#ffb900"/>
      </svg>
      Sign in with Microsoft 365
    </button>

  <p class="help-text">
  New student? <a href="register.php" class="help-link">Create an account</a>
</p>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
let pwVisible = false;

document.querySelectorAll('.role-tab').forEach(tab => {
  tab.addEventListener('click', function() {
    document.querySelectorAll('.role-tab').forEach(t => t.classList.remove('active'));
    this.classList.add('active');
    const role = this.dataset.role;
    document.getElementById('roleInput').value = role;
    
    const labels = {
      student: { text: 'Student ID', placeholder: 'e.g. 2024-00123' },
      faculty: { text: 'Faculty ID', placeholder: 'e.g. FAC-0045' },
      admin: { text: 'Admin Username', placeholder: 'e.g. admin.jdelacruz' }
    };
    
    document.getElementById('idLabel').textContent = labels[role].text;
    document.getElementById('idInput').placeholder = labels[role].placeholder;
    document.getElementById('idInput').value = '';
    document.getElementById('pwInput').value = '';
  });
});

function togglePw() {
  pwVisible = !pwVisible;
  const input = document.getElementById('pwInput');
  const icon = document.getElementById('eyeIcon');
  input.type = pwVisible ? 'text' : 'password';
  icon.innerHTML = pwVisible
    ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>'
    : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
}

function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
}

// Handle form submission via AJAX
document.getElementById('loginForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  
  // Get form data
  const student_id = document.getElementById('idInput').value;
  const password = document.getElementById('pwInput').value;
  const role = document.getElementById('roleInput').value;
  const remember = document.getElementById('remember').checked ? 1 : 0;
  
  // Validate
  if (!student_id || !password) {
    showToast('Please enter both ID and password');
    return;
  }
  
  const btn = document.getElementById('loginBtn');
  const originalContent = btn.innerHTML;
  
  btn.classList.add('loading');
  btn.innerHTML = 'Signing in...';
  
  try {
    // Create FormData and append values
    const formData = new URLSearchParams();
    formData.append('student_id', student_id);
    formData.append('password', password);
    formData.append('role', role);
    formData.append('remember', remember);
    
    const response = await fetch('backend/api.php?action=login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: formData.toString()
    });
    
    const data = await response.json();
    
    if (data.success) {
      showToast('Login successful! Redirecting...');
      setTimeout(() => {
        window.location.href = data.redirect || 'dashboard.php';
      }, 1000);
    } else {
      showToast(data.message || 'Login failed');
      btn.classList.remove('loading');
      btn.innerHTML = originalContent;
    }
  } catch (error) {
    console.error('Error:', error);
    showToast('An error occurred. Please try again.');
    btn.classList.remove('loading');
    btn.innerHTML = originalContent;
  }
});
</script>
</body>
</html>