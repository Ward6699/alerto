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

// Fetch hotline data
$stmt = $conn->prepare("SELECT * FROM hotlines WHERE hotline_id = ?");
$stmt->bind_param("i", $hotline_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error'] = "Hotline not found!";
    header("Location: admin_dashboard.php");
    exit();
}

$hotline = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $contact_number = trim($_POST['contact_number']);
    $agency_name = trim($_POST['agency_name']);
    $location = trim($_POST['location']);
    $updated_by = $_SESSION['user_id'];
    
    // Validate inputs
    if (empty($name) || empty($contact_number) || empty($agency_name) || empty($location)) {
        $_SESSION['error'] = "All fields are required!";
        header("Location: edit_hotlines.php?id=" . $hotline_id);
        exit();
    }
    
    // Update hotline - FIXED: Added updated_at = NOW()
    $stmt = $conn->prepare("UPDATE hotlines SET name = ?, contact_number = ?, agency_name = ?, location = ?, updated_by = ?, updated_at = NOW() WHERE hotline_id = ?");
    $stmt->bind_param("ssssii", $name, $contact_number, $agency_name, $location, $updated_by, $hotline_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Hotline updated successfully!";
        header("Location: admin_dashboard.php");
    } else {
        $_SESSION['error'] = "Error updating hotline: " . $conn->error;
        header("Location: edit_hotlines.php?id=" . $hotline_id);
    }
    
    $stmt->close();
    $conn->close();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Hotline | ALERTO</title>
   <link rel="icon" type="image/png" href="../images/browsericon.png">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Archivo+Narrow:wght@400;700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="admin-header">
        <h1>EDIT HOTLINE</h1>
        <div class="logo-container">
                <img src="../images/logo.png" alt="Alerto Logo" class="logo">
        </div>
    </header>

    <!-- Welcome Section -->
    <section class="welcome-section">
        <h2>Edit <span class="admin-name">Hotline Information</span></h2>
    </section>

    <!-- Navigation Buttons -->
    <nav class="admin-nav">
        <a href="admin_dashboard.php" class="nav-btn">BACK TO DASHBOARD</a>
        <a href="../hotlines.php" class="nav-btn">HOTLINES</a>
        <a href="../logout.php" class="nav-btn">LOG OUT</a>
    </nav>

    <!-- Error Messages -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php 
                echo htmlspecialchars($_SESSION['error']); 
                unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <!-- Edit Form -->
    <section class="manage-section">
        <h2>Edit <span class="red-text">Hotline</span></h2>
        
        <form action="edit_hotlines.php?id=<?php echo $hotline_id; ?>" method="POST" class="announcement-form">
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($hotline['name']); ?>" placeholder="e.g., Hospital, Police, Fire" required>
                </div>

                <div class="form-group">
                    <label for="contact_number">Hotline Number</label>
                    <input type="text" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($hotline['contact_number']); ?>" placeholder="e.g., (043) 123-4567" required>
                </div>

                <div class="form-group">
                    <label for="agency_name">Agency</label>
                    <input type="text" id="agency_name" name="agency_name" value="<?php echo htmlspecialchars($hotline['agency_name']); ?>" placeholder="e.g., Department of Health" required>
                </div>
                
                <div class="form-group">
                    <label for="location">Area</label>
                    <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($hotline['location']); ?>" placeholder="e.g., Taal, Batangas" required>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: center;">
                <button type="submit" class="add-btn">Update Hotline</button>
                <a href="admin_dashboard.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </section>

    <!-- Footer -->
    <footer class="admin-footer">
        <p>© Alerto 2025. All Right Reserved.</p>
    </footer>
</body>
</html>
<?php $conn->close(); ?>