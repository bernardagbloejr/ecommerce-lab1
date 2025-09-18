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
$required_fields = ['name', 'email', 'password', 'country', 'city', 'contact', 'user_role'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        $response['status'] = 'error';
        $response['message'] = 'Please fill in all required fields';
        echo json_encode($response);
        exit();
    }
}

// Sanitize input data
$name = trim($_POST['name']);
$email = trim($_POST['email']);
$password = $_POST['password'];
$country = trim($_POST['country']);
$city = trim($_POST['city']);
$contact = trim($_POST['contact']);
$user_role = (int)$_POST['user_role'];

// Server-side validation
// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['status'] = 'error';
    $response['message'] = 'Please enter a valid email address';
    echo json_encode($response);
    exit();
}

// Validate password strength
if (strlen($password) < 6 || !preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
    $response['status'] = 'error';
    $response['message'] = 'Password must be at least 6 characters with uppercase, lowercase, and number';
    echo json_encode($response);
    exit();
}

// Validate phone number (basic validation)
if (!preg_match('/^[\+]?[0-9\s\-\(\)]{10,15}$/', $contact)) {
    $response['status'] = 'error';
    $response['message'] = 'Please enter a valid phone number (10-15 digits)';
    echo json_encode($response);
    exit();
}

// Validate field lengths based on database schema
if (strlen($name) > 100) {
    $response['status'] = 'error';
    $response['message'] = 'Name must be 100 characters or less';
    echo json_encode($response);
    exit();
}

if (strlen($email) > 50) {
    $response['status'] = 'error';
    $response['message'] = 'Email must be 50 characters or less';
    echo json_encode($response);
    exit();
}

if (strlen($country) > 30) {
    $response['status'] = 'error';
    $response['message'] = 'Country must be 30 characters or less';
    echo json_encode($response);
    exit();
}

if (strlen($city) > 30) {
    $response['status'] = 'error';
    $response['message'] = 'City must be 30 characters or less';
    echo json_encode($response);
    exit();
}

if (strlen($contact) > 15) {
    $response['status'] = 'error';
    $response['message'] = 'Contact number must be 15 characters or less';
    echo json_encode($response);
    exit();
}

// Validate user role
if (!in_array($user_role, [1, 2])) {
    $user_role = 2; // Default to customer
}

// Attempt to register customer
$result = register_customer_ctr($name, $email, $password, $country, $city, $contact, $user_role);

if ($result['status'] === 'success') {
    $response['status'] = 'success';
    $response['message'] = 'Registration successful! Please login to continue.';
    $response['customer_id'] = $result['customer_id'];
} else {
    $response['status'] = 'error';
    $response['message'] = $result['message'];
}

echo json_encode($response);