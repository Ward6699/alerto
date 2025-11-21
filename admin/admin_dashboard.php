<?php
session_start();
include('../db_connection.php');

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../signin.php");
    exit();
}

// Fetch all announcements with user details
$sql_announcements = "SELECT a.*, 
        u1.name as added_by_name, 
        u2.name as updated_by_name 
        FROM announcements a
        LEFT JOIN users u1 ON a.posted_by = u1.user_id
        LEFT JOIN users u2 ON a.updated_by = u2.user_id
        ORDER BY a.created_at DESC";
$result_announcements = $conn->query($sql_announcements);

// Fetch all hotlines with user details
$sql_hotlines = "SELECT h.*, 
        u1.name as added_by_name, 
        u2.name as updated_by_name 
        FROM hotlines h
        LEFT JOIN users u1 ON h.added_by = u1.user_id
        LEFT JOIN users u2 ON h.updated_by = u2.user_id
        ORDER BY h.created_at DESC";
$result_hotlines = $conn->query($sql_hotlines);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | ALERTO</title>
    <link rel="icon" type="image/png" href="../images/browsericon.png">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Archivo+Narrow:wght@400;700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="admin-header">
        <h1>ADMIN DASHBOARD</h1>
        <div class="logo-container">
            <img src="../images/logo.png" alt="Alerto Logo" class="logo">
        </div>
    </header>

    <!-- Welcome Section -->
    <section class="welcome-section">
        <h2>Welcome, <span class="admin-name">Admin!</span></h2>
    </section>

    <!-- Navigation Buttons -->
    <nav class="admin-nav">
        <a href="../homepage.php" class="nav-btn">HOMEPAGE</a>
        <a href="../hotlines.php" class="nav-btn">HOTLINES</a>
        <a href="../logout.php" class="nav-btn">LOG OUT</a>
    </nav>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
                echo htmlspecialchars($_SESSION['success']); 
                unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php 
                echo htmlspecialchars($_SESSION['error']); 
                unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <!-- Manage Announcements Section -->
    <section class="manage-section">
        <h2>Manage <span class="red-text">Announcements</span></h2>
        
        <form action="create_announcements.php" method="POST" class="announcement-form">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" placeholder="e.g., Class Suspension Due to Typhoon" required>
                </div>

                <div class="form-group full-width">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" rows="3" placeholder="Brief description or additional details..." required></textarea>
                </div>

                <div class="form-group">
                    <label for="date">Suspension Date</label>
                    <input type="date" id="date" name="date" required>
                </div>

                <div class="form-group">
                    <label for="region">Region</label>
                    <input type="text" id="region" name="region" placeholder="e.g., Metro Manila" required>
                </div>
                
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" placeholder="e.g., Quezon City" required>
                </div>
                
                <div class="form-group">
                    <label for="level">Level</label>
                    <select id="level" name="level" required>
                        <option value="">Select Level</option>
                        <option value="All Levels">All Levels</option>
                        <option value="Preschool">Preschool</option>
                        <option value="Elementary">Elementary</option>
                        <option value="High School">High School</option>
                        <option value="Senior High School">Senior High School</option>
                        <option value="College">College</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="type">Type</label>
                    <select id="type" name="type" required>
                        <option value="">Select Type</option>
                        <option value="Public">Public</option>
                        <option value="Private">Private</option>
                        <option value="Both">Both</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="add-btn">Add Announcement</button>
        </form>
    </section>

    <!-- Admin Logs Section - Announcements -->
    <section class="logs-section">
        <h2>Admin Logs - Announcements</h2>
        
        <div class="table-container">
            <table class="logs-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Content</th>
                        <th>Date</th>
                        <th>Created At</th>
                        <th>Added By</th>
                        <th>Updated By</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_announcements->num_rows > 0): ?>
                        <?php while($row = $result_announcements->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['announcement_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td class="content-cell"><?php echo htmlspecialchars(substr($row['content'], 0, 50)) . '...'; ?></td>
                            <td><?php echo htmlspecialchars(date('M d, Y', strtotime($row['date']))); ?></td>
                            <td><?php echo htmlspecialchars(date('M d, Y H:i', strtotime($row['created_at']))); ?></td>
                            <td><?php echo htmlspecialchars($row['added_by_name']); ?></td>
                            <td><?php echo $row['updated_by_name'] ? htmlspecialchars($row['updated_by_name']) : '-'; ?></td>
                            <td><?php echo $row['updated_at'] ? htmlspecialchars(date('M d, Y H:i', strtotime($row['updated_at']))) : '-'; ?></td>
                            <td class="actions">
                                <a href="edit_announcements.php?id=<?php echo $row['announcement_id']; ?>" class="action-btn edit-btn">Edit</a>
                                <a href="delete_announcements.php?id=<?php echo $row['announcement_id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this announcement?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="no-data">No announcements yet</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Manage Hotlines Section -->
    <section class="manage-section">
        <h2>Manage <span class="red-text">Hotlines</span></h2>
        
        <form action="create_hotlines.php" method="POST" class="announcement-form">
            <div class="form-grid">
                <div class="form-group">
                    <label for="hotline_name">Name</label>
                    <input type="text" id="hotline_name" name="name" placeholder="e.g., Hospital, Police, Fire" required>
                </div>

                <div class="form-group">
                    <label for="contact_number">Hotline Number</label>
                    <input type="text" id="contact_number" name="contact_number" placeholder="e.g., (043) 123-4567" required>
                </div>

                <div class="form-group">
                    <label for="agency_name">Agency</label>
                    <input type="text" id="agency_name" name="agency_name" placeholder="e.g., Department of Health" required>
                </div>
                
                <div class="form-group">
                    <label for="hotline_location">Area</label>
                    <input type="text" id="hotline_location" name="location" placeholder="e.g., Taal, Batangas" required>
                </div>
            </div>

            <button type="submit" class="add-btn">Add Hotline</button>
        </form>
    </section>

    <!-- Admin Logs Section - Hotlines -->
    <section class="logs-section">
        <h2>Admin Logs - Hotlines</h2>
        
        <div class="table-container">
            <table class="logs-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Number</th>
                        <th>Agency</th>
                        <th>Area</th>
                        <th>Added By</th>
                        <th>Created At</th>
                        <th>Updated By</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_hotlines->num_rows > 0): ?>
                        <?php while($row = $result_hotlines->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['hotline_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['agency_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                            <td><?php echo htmlspecialchars($row['added_by_name']); ?></td>
                            <td><?php echo htmlspecialchars(date('M d, Y H:i', strtotime($row['created_at']))); ?></td>
                            <td><?php echo $row['updated_by_name'] ? htmlspecialchars($row['updated_by_name']) : '-'; ?></td>
                            <td><?php echo $row['updated_at'] ? htmlspecialchars(date('M d, Y H:i', strtotime($row['updated_at']))) : '-'; ?></td>
                            <td class="actions">
                                <a href="edit_hotlines.php?id=<?php echo $row['hotline_id']; ?>" class="action-btn edit-btn">Edit</a>
                                <a href="delete_hotlines.php?id=<?php echo $row['hotline_id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this hotline?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="no-data">No hotlines yet</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Footer -->
    <footer class="admin-footer">
        <p>© Alerto 2025. All Right Reserved.</p>
    </footer>
</body>
</html>
<?php $conn->close(); ?>