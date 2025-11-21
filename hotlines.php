<?php
session_start();

// If user is not logged in, redirect to signin page
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

// Include database connection
include('db_connection.php');

// Retrieve user data including profile picture
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, role, profile_picture FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Set session values
$name = $user['name'];
$role = $user['role'];
$profile_picture = $user['profile_picture'] ?? '';

// Fetch all hotlines from database
$sql = "SELECT * FROM hotlines ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Hotlines | ALERTO</title>
    <link rel="icon" type="image/png" href="images/browsericon.png">
    <link rel="stylesheet" href="css/hotlines.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Archivo+Narrow:wght@400;700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="close-btn" id="closeSidebar">
                <span>&times;</span>
            </div>
        </div>
        
          <a href="myprofile.php" class="user-profile-link">
            <div class="user-profile">
                <div class="user-avatar">
                    <?php if (!empty($profile_picture) && file_exists($profile_picture)): ?>
                        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    <?php else: ?>
                        <svg width="50" height="50" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" fill="black"/>
                            <path d="M12 14C7.58172 14 4 17.5817 4 22H20C20 17.5817 16.4183 14 12 14Z" fill="black"/>
                        </svg>
                    <?php endif; ?>
                </div>
                <div class="user-name">
                    <?php echo htmlspecialchars($name); ?>
                </div>
                <div class="user-role">
                    (<?php echo htmlspecialchars($role); ?>)
                </div>
            </div>
        </a>
        
        <nav class="sidebar-nav">
            <ul>
                <li><a href="homepage.php" class="nav-item">Home</a></li>
                <li><a href="guideequake.php" class="nav-item">Guide</a></li>
                <li><a href="hotlines.php" class="nav-item active">Emergency Hotlines</a></li>
                <li><a href="prep.php" class="nav-item">My Preparedness</a></li>
                <li><a href="about.php" class="nav-item">About</a></li>
            </ul>
        </nav>
        
        <div class="sidebar-footer">
            <form method="post" action="logout.php">
                <button type="submit" class="sign-out-btn">Sign Out</button>
            </form>
        </div>
    </div>

    <!-- Overlay for sidebar -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Header Section -->
    <header class="header">
        <div class="header-content">
            <div class="header-left">
                <div class="sidebar-icon">
                    <div class="hamburger" id="hamburger">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
                <div class="page-title">EMERGENCY HOTLINES</div>
            </div>
            <div class="logo-container">
                <a href="homepage.php">
                    <img src="images/logo.png" alt="Alerto Logo" class="logo">
                </a>
            </div>
        </div>
        <div class="header-line"></div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <!-- 911 Emergency Banner -->
        <section class="emergency-banner">
            <div class="banner-content">
                <div class="banner-left">
                    <div class="emergency-number">
                        <img src="images/ehnumber.jpg" alt="Phone" class="banner-icon">
                        <span class="number-text">911</span>
                    </div>
                </div>
                <div class="banner-middle">
                    <ul class="emergency-info">
                        <li>National Emergency Hotline</li>
                        <li>Available 24/7 Nationwide</li>
                        <li>Free to call, connects you to emergency services</li>
                    </ul>
                </div>
                <div class="banner-right">
                    <p class="view-all-text">For more details, click here:</p>
                    <a href="https://e911.gov.ph/emergency-hotline-numbers/" target="_blank" class="view-all-btn">View All</a>
                </div>
            </div>
        </section>

        <!-- Hotlines Grid -->
        <section class="hotlines-section">
            <?php if ($result && $result->num_rows > 0): ?>
                <div class="hotlines-grid">
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="hotline-card">
                            <h3 class="hotline-name"><?php echo htmlspecialchars($row['name']); ?></h3>
                            <div class="hotline-details">
                                <div class="detail-item">
                                    <img src="images/ehnumber.jpg" alt="Phone" class="detail-icon">
                                    <span class="hotline-number"><?php echo htmlspecialchars($row['contact_number']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <img src="images/ehagency.jpg" alt="Agency" class="detail-icon">
                                    <span class="detail-text"><?php echo htmlspecialchars($row['agency_name']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <img src="images/ehlocation.jpg" alt="Location" class="detail-icon">
                                    <span class="detail-text"><?php echo htmlspecialchars($row['location']); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="no-hotlines">
                    <p>No hotlines available yet. Check back soon!</p>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; Alerto 2025. All Rights Reserved.</p>
    </footer>

    <script src="js/hotlines.js"></script>
</body>
</html>
<?php $conn->close(); ?>