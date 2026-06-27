<?php
// ========================================
// LOGIN PAGE
// Users enter username/email and password
// ========================================

require_once 'config.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$message = '';

// Check if user just registered (success message)
if (isset($_GET['registered'])) {
    $message = "<div class='alert-success'>Registration successful! Please login below.</div>";
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validate empty fields
    if (empty($username) || empty($password)) {
        $message = "<div class='alert-error'>Please enter both username and password!</div>";
    } else {
        // Find user in database
        $sql = "SELECT id, username, email, password_hash, role 
                FROM users 
                WHERE username = '$username' OR email = '$username'";
        $result = $conn->query($sql);
        $user = $result->fetch_assoc();
        
        // ========================================
        // PASSWORD VERIFICATION
        // password_verify() compares entered password with stored hash
        // This is the most important security step!
        // ========================================
        if ($user && password_verify($password, $user['password_hash'])) {
            // Login successful - create session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Log login activity
            $log_sql = "INSERT INTO activity_log (user_id, username, action, table_name, details) 
                        VALUES (" . $user['id'] . ", '" . $user['username'] . "', 'LOGIN', 'users', 'User logged in')";
            $conn->query($log_sql);
            
            // Redirect to dashboard
            header('Location: index.php');
            exit;
        } else {
            // Same error message for security - don't reveal which part failed
            $message = "<div class='alert-error'>Invalid username or password!</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Marynissa Sari-Sari Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <div class="auth-card">
        <div class="logo">
            <h2>Marynissa Sari-Sari Store</h2>
            <p>Inventory Management System</p>
        </div>
        
        <?= $message ?>
        
        <form method="POST">
            <label>Username or Email</label>
            <input type="text" name="username" placeholder="Enter username or email" required>
            
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter password" required>
            
            <button type="submit" class="btn-login">Login</button>
            
            <div class="auth-link">
                Don't have an account? <a href="register.php">Register here</a>
            </div>
        </form>
    </div>
</body>
</html>