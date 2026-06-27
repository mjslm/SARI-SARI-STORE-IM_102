<?php
// ========================================
// AUTHENTICATION FUNCTIONS
// This file handles all login/security functions
// ========================================

// Start session to track logged-in users
session_start();

// ========================================
// REQUIRE USER TO BE LOGGED IN
// Redirects to login page if not logged in
// Use this on pages that need login
// ========================================
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

// ========================================
// CHECK IF USER IS LOGGED IN
// Returns true if logged in, false otherwise
// ========================================
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// ========================================
// CHECK IF USER IS ADMIN
// Returns true if admin, false otherwise
// ========================================
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

// ========================================
// REQUIRE ADMIN ACCESS
// Shows "Access denied" if user is not admin
// Use this on admin-only pages
// ========================================
function requireAdmin() {
    if (!isAdmin()) {
        die("<h2 style='color:red; text-align:center; margin-top:50px;'>Access Denied</h2>
             <p style='text-align:center;'>You do not have permission to view this page.</p>");
    }
}

// ========================================
// GET CURRENT USER'S USERNAME
// Returns username or null if not logged in
// ========================================
function getUsername() {
    return isset($_SESSION['username']) ? $_SESSION['username'] : null;
}
?>