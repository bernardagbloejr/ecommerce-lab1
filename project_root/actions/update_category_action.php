<?php

header('Content-Type: application/json');
session_start();

$response = array();

// Include required files
require_once '../settings/core.php';
require_once '../controllers/category_controller.php';

// Check if user is logged in and is admin
if (!is_logged_in()) {
    $response['status'] = 'error';
    $response['message'] = 'You must be logged in to access this feature';
    echo json_encode($response);
    exit();
}

if (!is_admin_user()) {
    $response['status'] = 'error';
    $response['message'] = 'Unauthorized access. Administrator privileges required';
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

// Validate required fields
if (!isset($_POST['category_id']) || !isset($_POST['category_name'])) {
    $response['status'] = 'error';
    $response['message'] = 'Missing required fields';
    echo json_encode($response);
    exit();
}

// Sanitize input data
$category_id = (int)$_POST['category_id'];
$category_name = trim($_POST['category_name']);

// Server-side validation
if ($category_id <= 0) {
    $response['status'] = 'error';
    $response['message'] = 'Invalid category ID';
    echo json_encode($response);
    exit();
}

if (empty($category_name)) {
    $response['status'] = 'error';
    $response['message'] = 'Category name is required';
    echo json_encode($response);
    exit();
}

// Validate category name length
if (strlen($category_name) > 100) {
    $response['status'] = 'error';
    $response['message'] = 'Category name must be 100 characters or less';
    echo json_encode($response);
    exit();
}

// Basic validation for category name (no special characters that could cause issues)
if (!preg_match('/^[a-zA-Z0-9\s\-_&()]+$/', $category_name)) {
    $response['status'] = 'error';
    $response['message'] = 'Category name contains invalid characters';
    echo json_encode($response);
    exit();
}

try {
    // Attempt to update category
    $result = update_category_ctr($category_id, $category_name);

    if ($result['status'] === 'success') {
        $response['status'] = 'success';
        $response['message'] = $result['message'];
        $response['category_id'] = $category_id;
        $response['category_name'] = $category_name;
    } else {
        $response['status'] = 'error';
        $response['message'] = $result['message'];
    }

} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = 'An error occurred while updating the category';
    
    // Log the error for debugging (in production, you'd want to log this properly)
    error_log("Category update error: " . $e->getMessage());
}

echo json_encode($response);