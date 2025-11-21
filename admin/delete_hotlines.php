<?php
session_start();
include('../db_connection.php');

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get hotline ID from URL
$hotline_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($hotline_id == 0) {
    $_SESSION['error'] = "Invalid hotline ID!";
    header("Location: admin_dashboard.php");
    exit();
}

// Delete hotline
$stmt = $conn->prepare("DELETE FROM hotlines WHERE hotline_id = ?");
$stmt->bind_param("i", $hotline_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $_SESSION['success'] = "Hotline deleted successfully!";
    } else {
        $_SESSION['error'] = "Hotline not found!";
    }
} else {
    $_SESSION['error'] = "Error deleting hotline: " . $conn->error;
}

$stmt->close();
$conn->close();

header("Location: admin_dashboard.php");
exit();
?>