<?php

header('Content-Type: application/json');
session_start();

$response = array();

// Check if user is already logged in
if (isset($_SESSION['customer_id'])) {
    $response['status'] = 'error';
    $response['message'] = 'You are already logged in';
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

require_once '../controllers/customer_controller.php';

// Validate required fields
$required_fields = ['email', 'password'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        $response['status'] = 'error';
        $response['message'] = 'Please fill in all required fields';
        echo json_encode($response);
        exit();
    }
}

// Sanitize input data
$email = trim($_POST['email']);
$password = $_POST['password'];

// Server-side validation
// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['status'] = 'error';
    $response['message'] = 'Please enter a valid email address';
    echo json_encode($response);
    exit();
}

// Basic password validation (not empty and reasonable length)
if (strlen($password) < 1) {
    $response['status'] = 'error';
    $response['message'] = 'Password cannot be empty';
    echo json_encode($response);
    exit();
}

// Attempt to login customer
$result = login_customer_ctr($email, $password);

if ($result['status'] === 'success') {
    // Set session variables for successful login
    $customer = $result['customer'];
    
    $_SESSION['customer_id'] = $customer['customer_id'];
    $_SESSION['customer_name'] = $customer['customer_name'];
    $_SESSION['customer_email'] = $customer['customer_email'];
    $_SESSION['user_role'] = $customer['user_role'];
    $_SESSION['customer_country'] = $customer['customer_country'];
    $_SESSION['customer_city'] = $customer['customer_city'];
    $_SESSION['customer_contact'] = $customer['customer_contact'];
    $_SESSION['customer_image'] = $customer['customer_image'];
    $_SESSION['login_time'] = time();
    
    $response['status'] = 'success';
    $response['message'] = 'Login successful! Welcome back.';
    $response['customer_id'] = $customer['customer_id'];
    $response['customer_name'] = $customer['customer_name'];
    $response['user_role'] = $customer['user_role'];
} else {
    $response['status'] = 'error';
    $response['message'] = $result['message'];
}

echo json_encode($response);