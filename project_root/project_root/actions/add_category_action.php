<?php
header('Content-Type: application/json');
session_start();

require_once '../settings/core.php';
require_once '../controllers/category_controller.php';

// Check if user is logged in and is admin
if (!is_logged_in() || !is_admin_user()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['category_name'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit();
}

$category_name = trim($_POST['category_name']);

if (empty($category_name)) {
    echo json_encode(['status' => 'error', 'message' => 'Category name is required']);
    exit();
}

$result = add_category_ctr($category_name);
echo json_encode($result);
?>