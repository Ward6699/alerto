<?php
session_start();

// Check if user is logged in
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

if (!$user) {
    session_destroy();
    header("Location: signin.php");
    exit();
}

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if ($_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $file_type = $_FILES['profile_picture']['type'];
        $file_size = $_FILES['profile_picture']['size'];
        
        if (!in_array($file_type, $allowed_types)) {
            $message = 'Invalid file type. Only JPG, PNG, and GIF are allowed.';
            $message_type = 'error';
        } elseif ($file_size > $max_size) {
            $message = 'File size exceeds 5MB limit.';
            $message_type = 'error';
        } else {
            // Create uploads directory if it doesn't exist
            $upload_dir = 'uploads/profile_pictures/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Generate unique filename
            $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $new_filename = 'profile_' . $user_id . '_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            // Delete old profile picture if exists
            if (!empty($user['profile_picture']) && file_exists($user['profile_picture'])) {
                unlink($user['profile_picture']);
            }
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                // Update database
                $stmt = $conn->prepare("UPDATE users SET profile_picture = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?");
                $stmt->bind_param("si", $upload_path, $user_id);
                
                if ($stmt->execute()) {
                    $user['profile_picture'] = $upload_path;
                    $message = 'Profile picture updated successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Database update failed.';
                    $message_type = 'error';
                }
                $stmt->close();
            } else {
                $message = 'Failed to upload file.';
                $message_type = 'error';
            }
        }
    } else {
        $message = 'Upload error occurred.';
        $message_type = 'error';
    }
    
    // Return JSON response for AJAX
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $message_type === 'success',
        'message' => $message,
        'profile_picture' => $user['profile_picture'] ?? ''
    ]);
    exit();
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $birthdate = $_POST['birthdate'];
    $address = trim($_POST['address']);
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($phone) || empty($birthdate) || empty($address)) {
        $message = 'All fields are required.';
        $message_type = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Invalid email format.';
        $message_type = 'error';
    } else {
        // Check if email is already taken by another user
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $message = 'Email is already taken by another user.';
            $message_type = 'error';
        } else {
            // Update user profile
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone_number = ?, birthdate = ?, address = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?");
            $stmt->bind_param("sssssi", $name, $email, $phone, $birthdate, $address, $user_id);
            
            if ($stmt->execute()) {
                // Update user data in memory
                $user['name'] = $name;
                $user['email'] = $email;
                $user['phone_number'] = $phone;
                $user['birthdate'] = $birthdate;
                $user['address'] = $address;
                
                // Update session if name changed
                $_SESSION['user_name'] = $name;
                
                $message = 'Profile updated successfully!';
                $message_type = 'success';
            } else {
                $message = 'Failed to update profile.';
                $message_type = 'error';
            }
        }
        $stmt->close();
    }
    
    // Return JSON response for AJAX
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $message_type === 'success',
        'message' => $message
    ]);
    exit();
}

// Default values
$name = htmlspecialchars($user['name'] ?? '');
$email = htmlspecialchars($user['email'] ?? '');
$phone = htmlspecialchars($user['phone_number'] ?? '');
$birthdate = htmlspecialchars($user['birthdate'] ?? '');
$address = htmlspecialchars($user['address'] ?? '');
$role = htmlspecialchars($user['role'] ?? 'user');
$profile_picture = $user['profile_picture'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | ALERTO</title>
    <link rel="icon" type="image/png" href="images/browsericon.png">
    <link rel="stylesheet" href="css/myprofile.css">
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
                <div class="page-title">MY PROFILE</div>
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
    <main class="profile-main">
        <div class="profile-container">
            <!-- Left Column - Profile Picture and Buttons -->
            <div class="profile-left">
                <div class="profile-picture-container">
                    <div class="profile-picture" id="profilePicture">
                        <?php if (!empty($profile_picture) && file_exists($profile_picture)): ?>
                            <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" id="profileImage">
                        <?php else: ?>
                            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" fill="black"/>
                                <path d="M12 14C7.58172 14 4 17.5817 4 22H20C20 17.5817 16.4183 14 12 14Z" fill="black"/>
                            </svg>
                        <?php endif; ?>
                    </div>
                    <input type="file" id="profileImageInput" accept="image/*" style="display: none;">
                </div>
                
                <button class="profile-btn" id="viewPreparednessBtn">View My Preparedness</button>
                
                <form method="post" action="logout.php">
                    <button type="submit" class="profile-btn sign-out-profile-btn">Sign Out</button>
                </form>
            </div>

            <!-- Right Column - Profile Information -->
            <div class="profile-right">
                <form class="profile-form" id="profileForm">
                    <div class="form-group">
                        <input type="text" id="name" name="name" placeholder="Name" value="<?php echo $name; ?>" readonly required>
                    </div>
                    
                    <div class="form-group">
                        <input type="email" id="email" name="email" placeholder="Email" value="<?php echo $email; ?>" readonly required>
                    </div>
                    
                    <div class="form-group">
                        <input type="tel" id="phone" name="phone" placeholder="Phone No." value="<?php echo $phone; ?>" readonly required>
                    </div>
                    
                    <div class="form-group">
                        <input type="date" id="birthdate" name="birthdate" placeholder="Birth Date" value="<?php echo $birthdate; ?>" readonly required>
                    </div>
                    
                    <div class="form-group">
                        <input type="text" id="address" name="address" placeholder="Address" value="<?php echo $address; ?>" readonly required>
                    </div>
                    
                    <div class="button-group">
                        <button type="button" class="edit-profile-btn" id="editProfileBtn">Edit Profile</button>
                        <button type="button" class="cancel-profile-btn" id="cancelProfileBtn" style="display: none;">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; Alerto 2025. All Rights Reserved.</p>
    </footer>

    <script src="js/myprofile.js"></script>
</body>
</html>