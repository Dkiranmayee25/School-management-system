<?php
session_start();

// 1. Database Connection
$conn = new mysqli("localhost", "root", "", "school");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 2. Handle Login Logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $user_type = trim($_POST['user_type']);

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username=? AND Role=?");
    $stmt->bind_param("ss", $username, $user_type);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user_type;
            $_SESSION['username'] = $username; // This is the dynamic name

            if ($user_type == "admin") { header("Location: dashboard.php"); }
            elseif ($user_type == "teacher") { header("Location: tdashboard.php"); }
            elseif ($user_type == "student") { header("Location: sdashboard.php"); }
            exit();
        } else {
            echo "<script>alert('Incorrect Password');</script>";
        }
    } else {
        echo "<script>alert('User not found as " . $user_type . "');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Access Portal | Green Valley School</title>
    <style>
        :root { --primary: #1e6091; --secondary: #184e77; --accent: #52b788; --bg-light: #f8fafc; --white: #ffffff; }
        body, html { margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; height: 100vh; overflow: hidden; background: var(--bg-light); }
        .main-container { display: flex; height: 100vh; }
        .info-side { flex: 1.2; background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%); color: white; padding: 80px; display: flex; flex-direction: column; justify-content: center; }
        .school-brand h1 { font-size: 52px; margin: 0; font-weight: 800; }
        .portal-side { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; }
        .login-card { background: var(--white); width: 100%; max-width: 400px; padding: 40px; border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.05); }
        .role-selector { display: flex; background: #f1f5f9; padding: 5px; border-radius: 12px; margin-bottom: 30px; }
        .role-btn { flex: 1; border: none; padding: 12px; border-radius: 10px; cursor: pointer; font-weight: 600; color: #64748b; background: transparent; transition: 0.3s; }
        .role-btn.active { background: var(--primary); color: white; }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px; }
        .input-group input { width: 100%; padding: 14px 16px; border: 1px solid #e2e8f0; border-radius: 12px; box-sizing: border-box; }
        .btn-login { width: 100%; padding: 16px; background: var(--accent); color: white; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s; }
        .btn-login:hover { background: #3fa373; transform: translateY(-2px); }
        .forgot-link { display: block; text-align: center; margin-top: 15px; font-size: 13px; color: var(--primary); text-decoration: none; font-weight: 600; }
        @media (max-width: 1024px) { .info-side { display: none; } }
    </style>
</head>
<body>
<div class="main-container">
    <div class="info-side">
        <div class="school-brand">
            <h1>Green Valley School</h1>
            <p>Knowledge | Discipline | Excellence</p>
        </div>
    </div>
    <div class="portal-side">
        <div class="login-card">
            <h2>Access Portal</h2>
            <form method="POST">
                <div class="role-selector">
                    <button type="button" class="role-btn" onclick="setRole(this, 'admin')">Admin</button>
                    <button type="button" class="role-btn active" onclick="setRole(this, 'teacher')">Teacher</button>
                    <button type="button" class="role-btn" onclick="setRole(this, 'student')">Student</button>
                </div>
                <input type="hidden" name="user_type" id="user_type_hidden" value="teacher">
                <div class="input-group">
                    <label id="user-label">Teacher ID / Email</label>
                    <input type="text" name="username" placeholder="Enter Username" required>
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-login">Login</button>
                <a href="forgot_password.php" class="forgot-link">Forgot Password?</a>
            </form>
        </div>
    </div>
</div>
<script>
    function setRole(btn, role) {
        document.querySelectorAll('.role-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('user_type_hidden').value = role;
        const displayRole = role.charAt(0).toUpperCase() + role.slice(1);
        document.getElementById('user-label').innerText = displayRole + ' ID / Email';
    }
</script>
</body>
</html>
