<?php
// ========================================
// LOGOUT - DESTROY SESSION
// ========================================

session_start();

// Log logout activity
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    require_once 'config.php';
    $log_sql = "INSERT INTO activity_log (user_id, username, action, table_name, details) 
                VALUES (" . $_SESSION['user_id'] . ", '" . $_SESSION['username'] . "', 'LOGOUT', 'users', 'User logged out')";
    $conn->query($log_sql);
}

// Destroy all session data
session_destroy();

// Redirect to login page
header('Location: login.php');
exit;
?>