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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Guide | ALERTO</title>
  <link rel="icon" type="image/png" href="images/browsericon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/guide.css">
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
                <li><a href="guideequake.php" class="nav-item active">Guide</a></li>
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
                <div class="page-title">DISASTER GUIDE</div>
            </div>
            <div class="logo-container">
                <a href="homepage.php">
                    <img src="images/logo.png" alt="Alerto Logo" class="logo">
                </a>
            </div>
        </div>
        <div class="header-line"></div>
    </header>


    <!-- Navigation -->
        <nav class="disaster-nav">
        <a href="guideequake.php" class="disaster-nav-item">
            <span class="disaster-nav-text">EARTHQUAKE</span>
        </a>
        <a href="guidefire.php" class="disaster-nav-item">
            <span class="disaster-nav-text">FIRE</span>
        </a>
        <a href="guideflood.php" class="disaster-nav-item active">
            <span class="disaster-nav-text">FLOOD</span>
        </a>
        <a href="guidetyphoon.php" class="disaster-nav-item">
            <span class="disaster-nav-text">TYPHOON</span>
        </a>
        <a href="guidevolcerup.php" class="disaster-nav-item">
            <span class="disaster-nav-text">VOLCANIC ERUPTION</span>
        </a>
    </nav>


    <main class="disaster-wrapper">
        <!-- Disaster Intro Section -->
        <section class="disaster-intro" id="disaster-section">
            <h2 class="main-title">FLOOD</h2>
            <div class="divider"></div>
            <h3 class="subtitle">WHAT IS A FLOOD?</h3>
            <p class="description">
                A flood occurs when water overflows onto normally dry land due to heavy rain, overflowing rivers, or storm surges.
            </p>
        </section>


        <!-- Disaster Carousel Section -->
        <section class="disaster-carousel-section" id="disaster-carousel">
            <div class="section-divider"></div>
            <h2 class="section-title">SEVERE FLOODS IN THE PHILIPPINES</h2>
           
            <div class="carousel-container">
                <button class="carousel-btn prev-btn" id="prevBtn" aria-label="Previous slide">
                    <span>❮</span>
                </button>
               
                <div class="carousel-wrapper">
                    <div class="carousel-track" id="carouselTrack">
                        <!-- Slide 1 - Moro Gulf -->
                        <div class="carousel-slide">
                            <img src="images/FL1.png" alt="Typhoon Gloring" class="slide-image">
                            <div class="slide-overlay"></div>
                            <div class="slide-content">
                                <h3 class="slide-title">Typhoon Gloring (The Great Philippine Flood of 1972)</h3>
                                <p class="slide-info">2 billion worth of damage</p>
                                <a href="https://mb.com.ph/2024/7/27/the-great-philippine-flood-of-1972"
                                   class="read-more-btn" target="_blank" rel="noopener">Read More</a>
                            </div>
                        </div>


                        <!-- Slide 2 - Luzon -->
                        <div class="carousel-slide">
                            <img src="images/FL2.png" alt="Typhoon Ondoy" class="slide-image">
                            <div class="slide-overlay"></div>
                            <div class="slide-content">
                                <h3 class="slide-title">Typhoon Ondoy</h3>
                                <p class="slide-info">11.1 billion worth of damage</p>
                                <a href="https://manilastandard.net/?p=314263060"
                                   class="read-more-btn" target="_blank" rel="noopener">Read More</a>
                            </div>
                        </div>


                        <!-- Slide 3 - Casiguran -->
                        <div class="carousel-slide">
                            <img src="images/FL3.png" alt="Typhoon Sendong" class="slide-image">
                            <div class="slide-overlay"></div>
                            <div class="slide-content">
                                <h3 class="slide-title">Typhoon Sendong</h3>
                                <p class="slide-info">12 billion worth of damage</p>
                                <a href="https://mindanews.com/photo-of-the-day/2021/12/photo-essay-looking-back-tropical-storm-sendong/"
                                   class="read-more-btn" target="_blank" rel="noopener">Read More</a>
                            </div>
                        </div>
                    </div>
                </div>
               
                <button class="carousel-btn next-btn" id="nextBtn" aria-label="Next slide">
                    <span>❯</span>
                </button>
               
                <!-- Dot Indicators -->
                <div class="carousel-dots" id="carouselDots">
                    <button class="dot active" aria-label="Go to slide 1" data-slide="0"></button>
                    <button class="dot" aria-label="Go to slide 2" data-slide="1"></button>
                    <button class="dot" aria-label="Go to slide 3" data-slide="2"></button>
                </div>
            </div>
        </section>


        <!-- What to Do Section -->
        <section class="what-to-do">
            <div class="section-divider"></div>
            <h2 class="section-title">WHAT TO DO?</h2>
           
            <!-- Before Section -->
            <div class="phase-section before-phase">
                <div class="phase-banner">
                    <h3 class="phase-title">BEFORE</h3>
                </div>
                <div class="advice-grid">
                    <div class="advice-card">
                        <h4 class="advice-title">Know Flood Zones</h4>
                        <p class="advice-text">Identify if your area is prone to flooding.</p>
                    </div>
                    <div class="advice-card">
                        <h4 class="advice-title">Elevate Belongings</h4>
                        <p class="advice-text">Move valuables and plugs above ground level.</p>
                    </div>
                    <div class="advice-card">
                        <h4 class="advice-title">Prepare Sandbags</h4>
                        <p class="advice-text">Block water entry points.</p>
                    </div>
                    <div class="advice-card">
                        <h4 class="advice-title">Secure Electricals</h4>
                        <p class="advice-text">Unplug devices and place them higher.</p>
                    </div>
                    <div class="advice-card">
                        <h4 class="advice-title">Have an Evacuation Plan</h4>
                        <p class="advice-text">Know safe routes and shelters.</p>
                    </div>
                    <div class="advice-card">
                        <h4 class="advice-title">Pack a Go Bag</h4>
                        <p class="advice-text">Include waterproof storage for documents.</p>
                    </div>
                </div>
            </div>


            <!-- During Section -->
            <div class="phase-section during-phase">
                <div class="phase-banner">
                    <h3 class="phase-title">DURING</h3>
                </div>
                <div class="advice-grid">
                    <div class="advice-card">
                        <h4 class="advice-title">Move to Higher Ground</h4>
                        <p class="advice-text">Evacuate quickly to safer areas.</p>
                    </div>
                    <div class="advice-card">
                        <h4 class="advice-title">Avoid Floodwater</h4>
                        <p class="advice-text">It may be contaminated or electrified.</p>
                    </div>
                    <div class="advice-card">
                        <h4 class="advice-title">Turn Off Utilities</h4>
                        <p class="advice-text">Cut power and gas if possible.</p>
                    </div>
                    <div class="advice-card">
                        <h4 class="advice-title">Listen to Authorities</h4>
                        <p class="advice-text">Follow local evacuation orders.</p>
                    </div>
                    <div class="advice-card">
                        <h4 class="advice-title">Do Not Drive Through Water</h4>
                        <p class="advice-text">Even shallow water can sweep you away.</p>
                    </div>
                    <div class="advice-card">
                        <h4 class="advice-title">Help Others Safely</h4>
                        <p class="advice-text">Prioritize the elderly and children.</p>
                    </div>
                </div>
            </div>


            <!-- After Section -->
            <div class="phase-section after-phase">
                <div class="phase-banner">
                    <h3 class="phase-title">AFTER</h3>
                </div>
                <div class="advice-grid">
                    <div class="advice-card">
                        <h4 class="advice-title">Avoid Dirty Water</h4>
                        <p class="advice-text">Clean and disinfect everything before use.</p>
                    </div>
                    <div class="advice-card">
                        <h4 class="advice-title">Inspect Home</h4>
                        <p class="advice-text">Check for structural damage before entering.</p>
                    </div>
                    <div class="advice-card">
                        <h4 class="advice-title">Boil Drinking Water</h4>
                        <p class="advice-text">Ensure safety from contamination.</p>
                    </div>
                    <div class="advice-card">
                        <h4 class="advice-title">Dispose of Spoiled Food</h4>
                        <p class="advice-text">Throw away anything soaked.</p>
                    </div>
                    <div class="advice-card">
                        <h4 class="advice-title">Dry the House</h4>
                        <p class="advice-text">Prevent mold growth.</p>
                    </div>
                    <div class="advice-card">
                        <h4 class="advice-title">Document and Report</h4>
                        <p class="advice-text">Inform authorities about damages.</p>
                    </div>
                </div>
            </div>
        </section>


        <!-- Emergency Kit Section -->
        <section class="emergency-kit">
            <div class="section-divider"></div>
            <h2 class="section-title">EMERGENCY KIT</h2>
            <div class="kit-grid">
                <div class="kit-item">
                    <div class="kit-image">
                        <img src="images/WATER.png" alt="Bottled Waters">
                    </div>
                    <p class="kit-label">Clean Drinking Water</p>
                </div>
                <div class="kit-item">
                    <div class="kit-image">
                        <img src="images/FOOD.png" alt="Non-perishable Food">
                    </div>
                    <p class="kit-label">Non-perishable Food</p>
                </div>
                <div class="kit-item">
                    <div class="kit-image">
                        <img src="images/AID.png" alt="First Aid Kit">
                    </div>
                    <p class="kit-label">First Aid Kit</p>
                </div>
                <div class="kit-item">
                    <div class="kit-image">
                        <img src="images/FLASHLIGHT.png" alt="Flashlight and Batteries">
                    </div>
                    <p class="kit-label">Waterproof Flashlight</p>
                </div>
                <div class="kit-item">
                    <div class="kit-image">
                        <img src="images/WATERPROOF.png" alt="Waterproof Bag">
                    </div>
                    <p class="kit-label">Waterproof Bag for Documents</p>
                </div>
                <div class="kit-item">
                    <div class="kit-image">
                        <img src="images/METALWHISTLE.PNG" alt="Whistle">
                    </div>
                    <p class="kit-label">Whistle</p>
                </div>
                <div class="kit-item">
                    <div class="kit-image">
                        <img src="images/CLOTHES.png" alt="Extra Clothes">
                    </div>
                    <p class="kit-label">Dry Clothes and Blanket</p>
                </div>
                <div class="kit-item">
                    <div class="kit-image">
                        <img src="images/RADIO.png" alt="Radio">
                    </div>
                    <p class="kit-label">Battery-Powered Radio</p>
                </div>
            </div>
        </section>
    </main>

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

    <script src="js/guide.js"></script>
</body>
</html>

