<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="styles.css">
    <title>Forgot Password - NITI Construction</title>
</head>
<body>
    <h1>Forgot Password</h1>
    <p>Enter your email to receive a password reset link:</p>
    <form method="POST" action="process_forgot_password.php">
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit" name="forgot_password">Submit</button>
    </form>
</body>
</html>
