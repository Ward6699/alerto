<?php
session_start();
include('../db_connection.php');

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = trim($_POST['name']);
    $contact_number = trim($_POST['contact_number']);
    $agency_name = trim($_POST['agency_name']);
    $location = trim($_POST['location']);
    $added_by = $_SESSION['user_id'];
    
    // Validate inputs
    if (empty($name) || empty($contact_number) || empty($agency_name) || empty($location)) {
        $_SESSION['error'] = "All fields are required!";
        header("Location: admin_dashboard.php");
        exit();
    }
    
    // Prepare and execute insert statement
    $stmt = $conn->prepare("INSERT INTO hotlines (name, contact_number, agency_name, location, added_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $name, $contact_number, $agency_name, $location, $added_by);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Hotline added successfully!";
    } else {
        $_SESSION['error'] = "Error adding hotline: " . $conn->error;
    }
    
    $stmt->close();
    $conn->close();
    
    header("Location: admin_dashboard.php");
    exit();
} else {
    header("Location: admin_dashboard.php");
    exit();
}
?>