<?php
session_start();
include('admin_auth.php');
include('../db_connection.php');

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get form data and sanitize
    $title = $conn->real_escape_string(trim($_POST['title']));
    $content = $conn->real_escape_string(trim($_POST['content']));
    $date = $conn->real_escape_string($_POST['date']);
    $region = $conn->real_escape_string(trim($_POST['region']));
    $location = $conn->real_escape_string(trim($_POST['location']));
    $level = $conn->real_escape_string($_POST['level']);
    $type = $conn->real_escape_string($_POST['type']);
    $posted_by = $_SESSION['user_id'];
    
    // Validate required fields
    if (empty($title) || empty($content) || empty($date) || empty($region) || empty($location) || empty($level) || empty($type)) {
        $_SESSION['error'] = "All fields are required!";
        header("Location: admin_dashboard.php");
        exit();
    }
    
    // Validate date format
    $date_obj = DateTime::createFromFormat('Y-m-d', $date);
    if (!$date_obj || $date_obj->format('Y-m-d') !== $date) {
        $_SESSION['error'] = "Invalid date format!";
        header("Location: admin_dashboard.php");
        exit();
    }
    
    // Prepare SQL statement - FIXED: Removed updated_at from INSERT
    $sql = "INSERT INTO announcements (title, content, region, location, date, level, type, posted_by, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("sssssssi", $title, $content, $region, $location, $date, $level, $type, $posted_by);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Announcement created successfully!";
        } else {
            $_SESSION['error'] = "Error creating announcement: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        $_SESSION['error'] = "Error preparing statement: " . $conn->error;
    }
    
} else {
    $_SESSION['error'] = "Invalid request method!";
}

$conn->close();
header("Location: admin_dashboard.php");
exit();
?>