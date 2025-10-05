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

$result = get_all_categories_ctr();
echo json_encode($result);
?>

