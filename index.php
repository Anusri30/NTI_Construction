<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// ✅ Updated Database connection
$conn = new mysqli('localhost', 'niti_user', 'password123', 'niti_db');

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Handle sign-up
if (isset($_POST['signup'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Validate role
    if (in_array($role, ['chief_engineer', 'site_engineer', 'client'])) {
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $username, $password, $role);
        if ($stmt->execute()) {
            echo "<script>alert('User registered successfully!');</script>";
        } else {
            echo "<script>alert('Error registering user: " . $conn->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Invalid role selected.');</script>";
    }
}

// Handle login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashedPassword, $role);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $role;

            // Redirect based on role
            if ($role === 'client') {
                header('Location: client_dashboard.php');
                exit;
            } else {
                header('Location: dashboard.php');
                exit;
            }
        } else {
            echo "<script>alert('Invalid username or password.');</script>";
        }
    } else {
        echo "<script>alert('Invalid username or password.');</script>";
    }
    $stmt->close();
}

// Dummy forgot password
if (isset($_POST['forgot_password'])) {
    echo "<script>alert('Password reset link sent to your email.');</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NITI Construction</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #111;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .container {
            background: #1e1e1e;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(255, 69, 0, 0.4);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h1 {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 30px;
            background: linear-gradient(90deg, #ff3c00, #ff9c00);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
            animation: glow 2s infinite alternate;
        }
        @keyframes glow {
            from {
                text-shadow: 2px 2px 4px rgba(255, 69, 0, 0.6);
            }
            to {
                text-shadow: 4px 4px 8px rgba(255, 156, 0, 0.8);
            }
        }
        form {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }
        input, select, button {
            padding: 12px;
            margin: 8px 0;
            border: none;
            border-radius: 6px;
            font-size: 16px;
        }
        input, select {
            background: #333;
            color: #fff;
        }
        button {
            background-color: #ff3c00;
            color: white;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background-color: #e63900;
        }
        .forgot {
            font-size: 14px;
            color: #ccc;
            margin-top: -6px;
            margin-bottom: 12px;
        }
        .forgot a {
            color: #ff3c00;
            text-decoration: none;
        }
        footer {
            font-size: 12px;
            margin-top: 20px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to<br>NITI Construction</h1>

        <!-- Login Form -->
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <div class="forgot">
                <a href="#" onclick="document.getElementById('forgot-form').style.display='block'; return false;">Forgot your password?</a>
            </div>
            <button type="submit" name="login">Login</button>
        </form>

        <!-- Forgot Password Form -->
        <form id="forgot-form" method="POST" style="display:none;">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit" name="forgot_password">Send Reset Link</button>
        </form>

        <!-- Signup Form -->
        <form method="POST">
            <input type="text" name="username" placeholder="New Username" required>
            <input type="password" name="password" placeholder="New Password" required>
            <select name="role" required>
                <option value="" disabled selected>Select Role</option>
                <option value="chief_engineer">Chief Engineer</option>
                <option value="site_engineer">Site Engineer</option>
                <option value="client">Client</option>
            </select>
            <button type="submit" name="signup">Sign Up</button>
        </form>

        <footer>&copy; 2025 NITI Construction. All Rights Reserved.</footer>
    </div>
</body>
</html>
