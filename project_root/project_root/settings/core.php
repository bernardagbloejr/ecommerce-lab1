<?php
session_start();

// For header redirection
ob_start();

// Function to check for login
function check_login() {
    if (!isset($_SESSION['customer_id'])) {
        header("Location: ../login/login.php");
        exit();
    }
}

// Function to get user ID
function get_user_id() {
    return isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : null;
}

// Function to get user role
function get_user_role() {
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
}

// Function to check for specific role (admin, customer, etc)
function check_role($required_role) {
    $user_role = get_user_role();
    if ($user_role !== $required_role) {
        header("Location: ../login/login.php");
        exit();
    }
}

// Function to check if user is admin
function is_admin() {
    return get_user_role() === 1;
}

// Function to check if user is customer
function is_customer() {
    return get_user_role() === 2;
}

// Auto-redirect if not logged in (optional - uncomment if needed)
// check_login();
?>