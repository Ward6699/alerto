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
  <title>About | ALERTO</title>
  <link rel="icon" type="image/png" href="images/browsericon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/about.css">
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
                <li><a href="hotlines.php" class="nav-item">Emergency Hotline</a></li>
                <li><a href="prep.php" class="nav-item">My Preparedness</a></li>
                <li><a href="about.php" class="nav-item active">About</a></li>
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
                <div class="page-title">ABOUT</div>
            </div>
            <div class="logo-container">
                <a href="homepage.php">
                    <img src="images/logo.png" alt="Alerto Logo" class="logo">
                </a>
            </div>
        </div>
        <div class="header-line"></div>
    </header>

  <main class="about-wrapper">
    <section class="what-is">
      <h2>WHAT IS ALERTO?</h2>
      <p>
        ALERTO! is an online platform designed to guide individuals in the Philippines in preparing for common
        natural disasters such as typhoons, earthquakes, and floods. It offers educational resources, emergency
        hotlines, and interactive tools that allow users to create their own contact lists and emergency kits. The
        platform also includes an admin feature that provides timely announcements and updates to help keep
        communities safe and well-informed.
      </p>
    </section>

    <section class="creators">
      <h2>WHO CREATED ALERTO?</h2>

      <div class="creator-grid">
        <article class="creator">
          <div class="avatar">
            <img src="images/busayong.jpg" alt="Ferdinand Philip Julius Busayong">
          </div>
          <h3 class="name">Ferdinand Philip Julius Busayong</h3>
          <div class="meta">
            <div><strong>BSIT</strong> 2<sup>nd</sup> Year</div>
            <div>TIP Manila</div>
            <div>Front-End Developer</div>
          </div>
        </article>

        <article class="creator">
          <div class="avatar">
            <img src="images/ramos.jpg" alt="Jan Shele Ramos">
          </div>
          <h3 class="name">Jan Shele Ramos</h3>
          <div class="meta">
            <div><strong>BSIT</strong> 2<sup>nd</sup> Year</div>
            <div>TIP Manila</div>
            <div>Documentation Specialist</div>
          </div>
        </article>

        <article class="creator">
          <div class="avatar">
            <img src="images/cascayo.jpg" alt="Karl Howard Cascayo">
          </div>
          <h3 class="name">Karl Howard Cascayo</h3>
          <div class="meta">
            <div><strong>BSIT</strong> 2<sup>nd</sup> Year</div>
            <div>TIP Manila</div>
            <div>Back-End Developer</div>
          </div>
        </article>
      </div>
    </section>
  </main>

 <!-- Footer -->
    <footer class="footer">
        <p>&copy; Alerto 2025. All Rights Reserved.</p>
    </footer>

  <script src="js/about.js"></script>
</body>
</html>