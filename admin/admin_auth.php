<?php
// This file checks if the logged-in user is an admin
// Include this at the top of every admin page

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Not logged in - redirect to signin page
    header("Location: ../signin.php");
    exit();
}

// Check if user has admin role
if ($_SESSION['role'] !== 'admin') {
    // Not an admin - redirect to homepage with error message
    header("Location: ../homepage.php");
    exit();
}

// If we reach here, user is logged in AND is an admin
// The page can continue loading
?>