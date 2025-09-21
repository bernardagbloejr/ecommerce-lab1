<?php

header('Content-Type: application/json');
session_start();

$response = array();

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    $response['status'] = 'error';
    $response['message'] = 'You are not logged in';
    echo json_encode($response);
    exit();
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit();
}

try {
    // Store user name for goodbye message
    $customer_name = isset($_SESSION['customer_name']) ? $_SESSION['customer_name'] : 'User';
    
    // Clear all session variables
    $_SESSION = array();
    
    // Delete session cookie if it exists
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Destroy the session
    session_destroy();
    
    $response['status'] = 'success';
    $response['message'] = 'Goodbye ' . $customer_name . '! You have been logged out successfully.';
    
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = 'An error occurred during logout. Please try again.';
}

echo json_encode($response);