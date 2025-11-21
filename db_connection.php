<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'u967494580_alerto');
define('DB_PASS', 'Alerto!1028');
define('DB_NAME', 'u967494580_alerto');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 for proper character support
$conn->set_charset("utf8mb4");
?>