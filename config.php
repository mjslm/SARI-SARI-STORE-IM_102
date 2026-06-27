<?php
// ========================================
// DATABASE CONNECTION
// This file connects to MySQL database
// ========================================

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'salomon_im102_final';

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character encoding for proper display
$conn->set_charset("utf8mb4");

// Include authentication functions
require_once 'auth.php';
?>