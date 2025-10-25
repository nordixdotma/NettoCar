<?php
// Session Configuration
session_start();

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check user role
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /auth/login.php");
        exit();
    }
}

// Redirect if not admin
function requireAdmin() {
    requireLogin();
    if (!hasRole('admin')) {
        header("Location: /index.php");
        exit();
    }
}

// Redirect if not agency
function requireAgency() {
    requireLogin();
    if (!hasRole('agency')) {
        header("Location: /index.php");
        exit();
    }
}

// Redirect if not client
function requireClient() {
    requireLogin();
    if (!hasRole('client')) {
        header("Location: /index.php");
        exit();
    }
}
?>
