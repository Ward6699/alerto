<?php
session_start();
include('admin_auth.php');
include('../db_connection.php');

// Get announcement ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "No announcement ID provided!";
    header("Location: admin_dashboard.php");
    exit();
}

$announcement_id = intval($_GET['id']);

// Verify announcement exists before deleting
$check_sql = "SELECT announcement_id FROM announcements WHERE announcement_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $announcement_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows == 0) {
    $_SESSION['error'] = "Announcement not found!";
    $check_stmt->close();
    $conn->close();
    header("Location: admin_dashboard.php");
    exit();
}
$check_stmt->close();

// Prepare DELETE statement
$sql = "DELETE FROM announcements WHERE announcement_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $announcement_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Announcement deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting announcement: " . $stmt->error;
    }
    
    $stmt->close();
} else {
    $_SESSION['error'] = "Error preparing statement: " . $conn->error;
}

$conn->close();
header("Location: admin_dashboard.php");
exit();
?>