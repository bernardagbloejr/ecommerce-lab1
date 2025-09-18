<?php
include_once 'settings/db_cred.php';
$conn = mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);
if ($conn) {
    echo "Database connected successfully";
} else {
    echo "Connection failed: " . mysqli_connect_error();
}
?>