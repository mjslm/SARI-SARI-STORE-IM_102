<?php
// ========================================
// REGISTER PAGE
// New users create an account
// ========================================

require_once 'config.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$message = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    
    $errors = [];
    
    // ========================================
    // VALIDATION CHECKS
    // All fields are validated before saving
    // ========================================
    
    // 1. Check if any field is empty
    if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
        $errors[] = "All fields are required!";
    }
    
    // 2. Username must be at least 3 characters
    if (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters!";
    }
    
    // 3. Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email!";
    }
    
    // 4. Check if passwords match
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match!";
    }
    
    // 5. Password must be at least 6 characters
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters!";
    }
    
    // If no errors, proceed with registration
    if (empty($errors)) {
        // Check if username or email already exists
        $check = $conn->query("SELECT id FROM users WHERE username = '$username' OR email = '$email'");
        
        if ($check->num_rows > 0) {
            $errors[] = "Username or email already exists!";
        } else {
            // ========================================
            // HASH THE PASSWORD BEFORE SAVING
            // This is the most important security step!
            // Password is never stored as plain text
            // ========================================
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            
            // Default role is 'staff' for new users
            $sql = "INSERT INTO users (username, email, password_hash, role) 
                    VALUES ('$username', '$email', '$hashed', 'staff')";
            
            if ($conn->query($sql)) {
                // Log registration activity (using system as user_id 0 since not logged in yet)
                $new_user_id = $conn->insert_id;
                $log_sql = "INSERT INTO activity_log (user_id, username, action, table_name, record_id, details) 
                            VALUES (0, '$username', 'REGISTER', 'users', $new_user_id, 'User registered: $username')";
                $conn->query($log_sql);
                
                // Registration successful - redirect to login
                header('Location: login.php?registered=1');
                exit;
            } else {
                $errors[] = "Database error: " . $conn->error;
            }
        }
    }
    
    // Display errors if any
    if (!empty($errors)) {
        $message = "<div class='alert-error'>" . implode("<br>", $errors) . "</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - Marynissa Sari-Sari Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <div class="auth-card">
        <div class="logo">
            <h2>Create Account</h2>
            <p>Join our inventory system</p>
        </div>
        
        <?= $message ?>
        
        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" placeholder="Choose a username" required>
            <div class="hint">Min 3 characters</div>
            
            <label>Email</label>
            <input type="email" name="email" placeholder="Enter your email" required>
            
            <label>Password</label>
            <input type="password" name="password" placeholder="Create a password" required>
            <div class="hint">Min 6 characters</div>
            
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" placeholder="Re-enter password" required>
            
            <button type="submit" class="btn-login">Register</button>
            
            <div class="auth-link">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </form>
    </div>
</body>
</html>