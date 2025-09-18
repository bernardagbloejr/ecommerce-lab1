<?php
//Database credentials
// Settings/db_cred.php

// define('DB_HOST', 'localhost');
// define('DB_USER', 'root');
// define('DB_PASS', '');
// define('DB_NAME', 'dbforlab');


if (!defined("SERVER")) {
    define("SERVER", "localhost");
}

if (!defined("USERNAME")) {
    define("USERNAME", "root");
}

if (!defined("PASSWD")) {
    define("PASSWD", "root"); // or "" for XAMPP
}

if (!defined("DATABASE")) {
    define("DATABASE", "shoppn");
}

// For MAMP, you might need to add port
if (!defined("PORT")) {
    define("PORT", "8889"); // MAMP default, 3306 for XAMPP
}
?>