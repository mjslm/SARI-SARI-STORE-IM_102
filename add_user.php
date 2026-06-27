<?php
require_once 'config.php';
requireAdmin();

$message = '';
$username = $email = '';
$selected_role = 'staff';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $selected_role = $_POST['role'] ?? 'staff';
    
    $errors = [];
    
    if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
        $errors[] = "All fields are required!";
    }
    if (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters!";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email!";
    }
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match!";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters!";
    }
    
    if (empty($errors)) {
        $check = $conn->query("SELECT id FROM users WHERE username = '$username' OR email = '$email'");
        
        if ($check->num_rows > 0) {
            $errors[] = "Username or email already exists!";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO users (username, email, password_hash, role) VALUES ('$username', '$email', '$hashed', '$selected_role')";
            
            if ($conn->query($sql)) {
                // Log the activity
                $user_id = $_SESSION['user_id'];
                $logged_user = $_SESSION['username'];
                $new_user_id = $conn->insert_id;
                $log_sql = "INSERT INTO activity_log (user_id, username, action, table_name, record_id, details) 
                            VALUES ($user_id, '$logged_user', 'ADD', 'users', $new_user_id, 'Added user: $username')";
                $conn->query($log_sql);
                
                header('Location: users.php?added=1');
                exit;
            } else {
                $errors[] = "Database error: " . $conn->error;
            }
        }
    }
    
    if (!empty($errors)) {
        $message = "<div class='alert-error'><strong>Please fix:</strong><br>• " . implode("<br>• ", $errors) . "</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add User - Sari-Sari Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="register-page">
    <div class="register-container">
        <h2>Add New User</h2>
        <p class="subtitle">Create a new user account</p>
        
        <?= $message ?>
        
        <form method="POST" action="add_user.php">
            <label>Username <span class="required">*</span></label>
            <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" required>
            <div class="hint">Min 3 characters</div>
            
            <label>Email <span class="required">*</span></label>
            <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            
            <label>Password <span class="required">*</span></label>
            <input type="password" name="password" required>
            <div class="hint">Min 6 characters</div>
            
            <label>Confirm Password <span class="required">*</span></label>
            <input type="password" name="confirm_password" required>
            
            <label>Role</label>
            <select name="role">
                <option value="staff" <?= $selected_role == 'staff' ? 'selected' : '' ?>>Staff</option>
                <option value="admin" <?= $selected_role == 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit">Add User</button>
                <a href="users.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>