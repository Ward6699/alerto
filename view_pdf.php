<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

// Get filename from URL
$filename = isset($_GET['file']) ? $_GET['file'] : '';

// Validate filename
if (empty($filename)) {
    die('No file specified.');
}

// Sanitize filename to prevent directory traversal
$filename = basename($filename);

// Build filepath
$filepath = 'reports/' . $filename;

// Check if file exists
if (!file_exists($filepath)) {
    die('File not found.');
}

// Security: Check if the file belongs to the current user
$user_id = $_SESSION['user_id'];
if (strpos($filename, '_' . $user_id . '_') === false) {
    die('Unauthorized access.');
}

// Get file content
$pdf_content = file_get_contents($filepath);

// Set headers for PDF display
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Content-Length: ' . strlen($pdf_content));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Output PDF content
echo $pdf_content;
exit();
?>