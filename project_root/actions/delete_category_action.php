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

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['category_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit();
}

$category_id = (int)$_POST['category_id'];

if ($category_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid category ID']);
    exit();
}

$result = delete_category_ctr($category_id);
echo json_encode($result);
?>