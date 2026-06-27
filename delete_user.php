<?php
require_once 'config.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);

// Prevent deleting yourself
if ($id == $_SESSION['user_id']) {
    header('Location: users.php?error=self');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $conn->query("SELECT username FROM users WHERE id = $id");
    $deleted_user = $result->fetch_assoc();
    
    $conn->query("DELETE FROM users WHERE id = $id");
    
    // Log the activity
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $log_sql = "INSERT INTO activity_log (user_id, username, action, table_name, record_id, details) 
                VALUES ($user_id, '$username', 'DELETE', 'users', $id, 'Deleted user: " . $deleted_user['username'] . "')";
    $conn->query($log_sql);
    
    header('Location: users.php?deleted=1');
    exit;
}

$result = $conn->query("SELECT id, username, email, role FROM users WHERE id = $id");
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Delete User</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container delete-container">
    <h1>Delete User</h1>
    
    <p style="font-size:1em; margin:25px 0 10px 0; color:#555;">Are you sure you want to delete this user:</p>
    
    <p class="delete-name">
        <?= htmlspecialchars($user['username']) ?>
    </p>
    
    <p class="delete-details">
        <?= htmlspecialchars($user['email']) ?> 
        | Role: <?= htmlspecialchars($user['role']) ?>
    </p>
    
    <p class="delete-warning">This action cannot be undone.</p>

    <form method="POST" style="display:inline;">
        <button type="submit" class="btn-delete-yes">Yes, Delete User</button>
    </form>
    <a href="users.php" class="btn-delete-cancel">Cancel</a>
</div>
</body>
</html>