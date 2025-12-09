<?php
session_start();

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = 'Bang1ad3sh';
$db = 'dcma';

// Create database connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection and handle errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        $_SESSION['error_message'] = "You do not have permission to access that page.";
        header("Location: /login.php"); 
        exit();
    }
}

function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

?>
