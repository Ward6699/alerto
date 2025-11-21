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

// Handle form submission (UPDATE)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get form data and sanitize
    $title = $conn->real_escape_string(trim($_POST['title']));
    $content = $conn->real_escape_string(trim($_POST['content']));
    $date = $conn->real_escape_string($_POST['date']);
    $region = $conn->real_escape_string(trim($_POST['region']));
    $location = $conn->real_escape_string(trim($_POST['location']));
    $level = $conn->real_escape_string($_POST['level']);
    $type = $conn->real_escape_string($_POST['type']);
    $updated_by = $_SESSION['user_id'];
    
    // Validate required fields
    if (empty($title) || empty($content) || empty($date) || empty($region) || empty($location) || empty($level) || empty($type)) {
        $_SESSION['error'] = "All fields are required!";
        header("Location: edit_announcements.php?id=" . $announcement_id);
        exit();
    }
    
    // Validate date format
    $date_obj = DateTime::createFromFormat('Y-m-d', $date);
    if (!$date_obj || $date_obj->format('Y-m-d') !== $date) {
        $_SESSION['error'] = "Invalid date format!";
        header("Location: edit_announcements.php?id=" . $announcement_id);
        exit();
    }
    
    // Prepare UPDATE statement
    $sql = "UPDATE announcements 
            SET title = ?, content = ?, region = ?, location = ?, date = ?, level = ?, type = ?, updated_by = ?, updated_at = NOW() 
            WHERE announcement_id = ?";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("sssssssii", $title, $content, $region, $location, $date, $level, $type, $updated_by, $announcement_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Announcement updated successfully!";
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Error updating announcement: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        $_SESSION['error'] = "Error preparing statement: " . $conn->error;
    }
}

// Fetch announcement data for the form
$sql = "SELECT * FROM announcements WHERE announcement_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $announcement_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error'] = "Announcement not found!";
    header("Location: admin_dashboard.php");
    exit();
}

$announcement = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Announcement | ALERTO</title>
    <link rel="icon" type="image/png" href="../images/browsericon.png">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Archivo+Narrow:wght@400;700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="admin-header">
        <h1>EDIT ANNOUNCEMENT</h1>
        <div class="logo-container">
                <img src="../images/logo.png" alt="Alerto Logo" class="logo">
        </div>
    </header>

    <!-- Navigation Buttons -->
    <nav class="admin-nav">
        <a href="admin_dashboard.php" class="nav-btn">BACK TO DASHBOARD</a>
        <a href="../homepage.php" class="nav-btn">HOMEPAGE</a>
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

    <!-- Edit Announcement Section -->
    <section class="manage-section">
        <h2>Edit <span class="red-text">Announcement</span></h2>
        
        <form action="edit_announcements.php?id=<?php echo $announcement_id; ?>" method="POST" class="announcement-form">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($announcement['title']); ?>" required>
                </div>

                <div class="form-group full-width">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" rows="3" required><?php echo htmlspecialchars($announcement['content']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="date">Suspension Date</label>
                    <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($announcement['date']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="region">Region</label>
                    <input type="text" id="region" name="region" value="<?php echo htmlspecialchars($announcement['region']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($announcement['location']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="level">Level</label>
                    <select id="level" name="level" required>
                        <option value="">Select Level</option>
                        <option value="All Levels" <?php echo ($announcement['level'] == 'All Levels') ? 'selected' : ''; ?>>All Levels</option>
                        <option value="Preschool" <?php echo ($announcement['level'] == 'Preschool') ? 'selected' : ''; ?>>Preschool</option>
                        <option value="Elementary" <?php echo ($announcement['level'] == 'Elementary') ? 'selected' : ''; ?>>Elementary</option>
                        <option value="High School" <?php echo ($announcement['level'] == 'High School') ? 'selected' : ''; ?>>High School</option>
                        <option value="Senior High School" <?php echo ($announcement['level'] == 'Senior High School') ? 'selected' : ''; ?>>Senior High School</option>
                        <option value="College" <?php echo ($announcement['level'] == 'College') ? 'selected' : ''; ?>>College</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="type">Type</label>
                    <select id="type" name="type" required>
                        <option value="">Select Type</option>
                        <option value="Public" <?php echo ($announcement['type'] == 'Public') ? 'selected' : ''; ?>>Public</option>
                        <option value="Private" <?php echo ($announcement['type'] == 'Private') ? 'selected' : ''; ?>>Private</option>
                        <option value="Both" <?php echo ($announcement['type'] == 'Both') ? 'selected' : ''; ?>>Both</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: center;">
                <button type="submit" class="add-btn">Update Announcement</button>
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