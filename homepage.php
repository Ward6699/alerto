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

// Fetch all announcements grouped by region and date
$sql = "SELECT * FROM announcements ORDER BY date DESC, region, location";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

// Group announcements by date first, then by region
$announcements_by_date = [];
while ($row = $result->fetch_assoc()) {
    $date = $row['date'];
    $region = $row['region'];
    
    if (!isset($announcements_by_date[$date])) {
        $announcements_by_date[$date] = [];
    }
    if (!isset($announcements_by_date[$date][$region])) {
        $announcements_by_date[$date][$region] = [];
    }
    $announcements_by_date[$date][$region][] = $row;
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | ALERTO</title>
    <link rel="icon" type="image/png" href="images/browsericon.png">
    <link rel="stylesheet" href="css/homepage.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Archivo+Narrow:wght@400;700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
  
    <style>
        .announcement-date-header {
            font-size: 1.3em;
            font-weight: 700;
            color: #d32f2f;
            margin: 30px 0 15px 0;
            padding: 10px 0;
            border-bottom: 2px solid #d32f2f;
        }
        
        .announcement-content-text {
            font-size: 0.9em;
            color: #555;
            margin: 8px 0;
            padding: 8px 12px;
            background-color: #f5f5f5;
            border-left: 3px solid #d32f2f;
            font-style: italic;
        }
        
        .region-header {
            margin-top: 20px;
        }

        .announcement-title {
            font-size: 1.1em;
            color: #333;
            margin: 20px 0 15px 0;
            padding: 12px 20px;
            background-color: #f5f5f5;
            border-left: 3px solid #d32f2f;
            font-style: normal;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
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
                <li><a href="homepage.php" class="nav-item active">Home</a></li>
                <li><a href="guideequake.php" class="nav-item">Guide</a></li>
                <li><a href="hotlines.php" class="nav-item">Emergency Hotline</a></li>
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
                <div class="page-title">HOME</div>
            </div>
            <div class="logo-container">
                <a href="homepage.php">
                    <img src="images/logo.png" alt="Alerto Logo" class="logo">
                </a>
            </div>
        </div>
        <div class="header-line"></div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-image">
            <img src="images/main.jpg" alt="First responder in a disaster scene">
        </div>
        <div class="hero-overlay">
            <div class="hero-text">
                <h1>HANDA.</h1>
                <h1>LIGTAS.</h1>
                <h1>ALERTO.</h1>
            </div>
            <div class="hero-buttons">
                <a href="#announcements" class="btn btn-primary">View Announcements</a>
                <a href="#disasters" class="btn btn-primary">Explore Website</a>
            </div>
        </div>
    </section>

    <!-- Announcement Section -->
    <section id="announcements" class="announcement-section">
        <div class="announcement-container">
            <div class="announcement-header">
                <h2>ANNOUNCEMENT</h2>
                <div class="announcement-date" id="current-date"></div>
            </div>
            <div class="announcement-content">
                
                <?php if (empty($announcements_by_date)): ?>
                    <!-- No announcements -->
                    <h3>CLASS SUSPENSIONS</h3>
                    <div class="announcement-table-container">
                        <table class="announcement-table">
                            <thead>
                                <tr>
                                    <th>Location</th>
                                    <th>Level</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="3" class="no-announcement">No announcements as of the moment</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <!-- Display announcements grouped by date and region -->
                    <?php foreach ($announcements_by_date as $date => $regions): ?>
                        <div class="announcement-date-header">
                            <?php echo htmlspecialchars(date('F d, Y (l)', strtotime($date))); ?>
                        </div>
                        
                        <?php foreach ($regions as $region => $announcements): ?>
                            <?php 
                            // Get the title and content from the first announcement in this region
                            $title = $announcements[0]['title'];
                            $content = $announcements[0]['content'];
                            ?>
                            
                            <!-- Display Title with new styling -->
                            <div class="announcement-title">
                                <?php echo htmlspecialchars($title); ?>
                            </div>
                            
                            <h4 class="region-header"><?php echo htmlspecialchars(strtoupper($region)); ?></h4>
                            
                            <?php if (!empty($content)): ?>
                                <div class="announcement-content-text">
                                    <?php echo htmlspecialchars($content); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="announcement-table-container">
                                <table class="announcement-table">
                                    <thead>
                                        <tr>
                                            <th>Location</th>
                                            <th>Level</th>
                                            <th>Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($announcements as $announcement): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($announcement['location']); ?></td>
                                                <td><?php echo htmlspecialchars($announcement['level']); ?></td>
                                                <td><?php echo htmlspecialchars($announcement['type']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <p class="refresh-text">Refresh this site to see the updated list.</p>
            </div>
        </div>
    </section>

    <!-- Disasters Section -->
    <section id="disasters" class="disasters-section">
        <!-- Earthquake -->
        <div class="disaster-item">
            <div class="disaster-image">
                <img src="images/earthquake.jpeg" alt="Earthquake disaster scene">
            </div>
            <div class="disaster-overlay right-aligned">
                <h2>WHEN THE EARTH SHAKES, <br> BE READY, STAY SAFE.</h2>
                <p>CHECK OUR GUIDE FOR SAFETY STEPS:</p>
                <a href="guideequake.php" class="btn btn-disaster">View Guide</a>
            </div>
        </div>
        <div class="disaster-divider"></div>

        <!-- Fire -->
        <div class="disaster-item">
            <div class="disaster-image">
                <img src="images/fire.jpeg" alt="Fire disaster scene">
            </div>
            <div class="disaster-overlay left-aligned">
                <h2>EVACUATE, <br> BEFORE IT'S TOO LATE.</h2>
                <p>CHECK OUR GUIDE FOR SAFETY STEPS:</p>
                <a href="guidefire.php" class="btn btn-disaster">View Guide</a>
            </div>
        </div>
        <div class="disaster-divider"></div>

        <!-- Flood -->
        <div class="disaster-item">
            <div class="disaster-image">
                <img src="images/flood.jpeg" alt="Flood disaster scene">
            </div>
            <div class="disaster-overlay right-aligned">
                <h2>WHEN WATERS RISE, <br>CLIMB TO SURVIVE.</h2>
                <p>CHECK OUR GUIDE FOR SAFETY STEPS:</p>
                <a href="guideflood.php" class="btn btn-disaster">View Guide</a>
            </div>
        </div>
        <div class="disaster-divider"></div>

        <!-- Typhoon -->
        <div class="disaster-item">
            <div class="disaster-image">
                <img src="images/typhoon.jpeg" alt="Typhoon disaster scene">
            </div>
            <div class="disaster-overlay left-aligned">
                <h2>STRONG WINDS MAY ROAR, <br>BUT READINESS MEANS MORE.</h2>
                <p>CHECK OUR GUIDE FOR SAFETY STEPS:</p>
                <a href="guidetyphoon.php" class="btn btn-disaster">View Guide</a>
            </div>
        </div>
        <div class="disaster-divider"></div>

        <!-- Volcanic Eruption -->
        <div class="disaster-item">
            <div class="disaster-image">
                <img src="images/volcanic_eruption.jpeg" alt="Volcanic eruption scene">
            </div>
            <div class="disaster-overlay right-aligned">
                <h2>WHEN THE VOLCANOES ERUPT, <br>BE ALERT, BE ABRUPT.</h2>
                <p>CHECK OUR GUIDE FOR SAFETY STEPS:</p>
                <a href="guidevolcerup.php" class="btn btn-disaster">View Guide</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <!-- IMPROVED FOOTER -->
    <footer class="footer">
        <div class="footer-content">
            <!-- Logo & About Section -->
            <div class="footer-logo-section">
                <div class="footer-logo">
                    <img src="images/flogo.png" alt="Alerto Logo">
                </div>
                <p class="footer-tagline">
                    Stay prepared, stay safe. Your companion for disaster readiness and emergency response.
                </p>
            </div>

            <!-- Contact Section -->
            <div class="footer-section">
                <h4>Get in Touch</h4>
                <a class="contact-item">
                    <img src="images/femail.png" alt="Email" class="contact-icon">
                    sample@gmail.com
                </a>
                <a class="contact-item">
                    <img src="images/fcontact.png" alt="Phone" class="contact-icon">
                    +63 999 999 9990
                </a>
            </div>

            <!-- Feedback Section -->
            <div class="footer-section">
                <h4>Drop us a Feedback</h4>
                <p class="footer-description">
                    Help us improve! Share your thoughts and suggestions.
                </p>
                <a href="https://forms.gle/aCp99DRQ9u2Ybt2T6" class="feedback-btn">Send Feedback</a>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <p>&copy; Alerto 2025. All Rights Reserved.</p>
            <a href="#top" class="return-top-btn" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;">
                Return to Top
            </a>
        </div>
    </footer>
    <script src="js/homepage.js"></script>
</body>
</html>