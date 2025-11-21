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

// Handle AJAX requests for CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'];
    
    // ==================== EMERGENCY CONTACTS CRUD ====================
    
    // CREATE - Add new contact
    if ($action === 'add_contact') {
        $contact_name = mysqli_real_escape_string($conn, $_POST['contact_name']);
        $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);
        $relationship = mysqli_real_escape_string($conn, $_POST['relationship']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        
        $sql = "INSERT INTO emergency_contacts (user_id, name, relation, phone_number, address, created_at, updated_at) 
                VALUES ('$user_id', '$contact_name', '$relationship', '$contact_number', '$address', NOW(), NOW())";
        
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true, 'id' => mysqli_insert_id($conn)]);
        } else {
            echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
        }
        mysqli_close($conn);
        exit();
    }
    
    // UPDATE - Edit existing contact
    if ($action === 'update_contact') {
        $contact_id = intval($_POST['contact_id']);
        $contact_name = mysqli_real_escape_string($conn, $_POST['contact_name']);
        $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);
        $relationship = mysqli_real_escape_string($conn, $_POST['relationship']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        
        $sql = "UPDATE emergency_contacts SET 
                name = '$contact_name',
                phone_number = '$contact_number',
                relation = '$relationship',
                address = '$address',
                updated_at = NOW()
                WHERE contact_id = '$contact_id' AND user_id = '$user_id'";
        
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
        }
        mysqli_close($conn);
        exit();
    }
    
    // DELETE - Remove contact
    if ($action === 'delete_contact') {
        $contact_id = intval($_POST['contact_id']);
        
        $sql = "DELETE FROM emergency_contacts WHERE contact_id = '$contact_id' AND user_id = '$user_id'";
        
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
        }
        mysqli_close($conn);
        exit();
    }
    
    // READ - Get all contacts for current user
    if ($action === 'get_contacts') {
        $sql = "SELECT * FROM emergency_contacts WHERE user_id = '$user_id' ORDER BY contact_id ASC";
        $result = mysqli_query($conn, $sql);
        
        $contacts = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $contacts[] = $row;
        }
        
        echo json_encode(['success' => true, 'contacts' => $contacts]);
        mysqli_close($conn);
        exit();
    }
    
    // ==================== EMERGENCY KIT CRUD ====================
    
    // CREATE - Add new kit item
    if ($action === 'add_kit_item') {
        $category = mysqli_real_escape_string($conn, $_POST['category']);
        $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
        $quantity = intval($_POST['quantity']);
        
        $sql = "INSERT INTO emergency_kit (user_id, item_name, category, quantity, created_at, updated_at) 
                VALUES ('$user_id', '$item_name', '$category', '$quantity', NOW(), NOW())";
        
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true, 'id' => mysqli_insert_id($conn)]);
        } else {
            echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
        }
        mysqli_close($conn);
        exit();
    }
    
    // READ - Get all kit items for current user
    if ($action === 'get_kit_items') {
        $sql = "SELECT * FROM emergency_kit WHERE user_id = '$user_id' ORDER BY category ASC, item_id ASC";
        $result = mysqli_query($conn, $sql);
        
        $items = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = $row;
        }
        
        echo json_encode(['success' => true, 'items' => $items]);
        mysqli_close($conn);
        exit();
    }
    
    // UPDATE - Edit kit item
    if ($action === 'update_kit_item') {
        $item_id = intval($_POST['item_id']);
        $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
        $category = mysqli_real_escape_string($conn, $_POST['category']);
        $quantity = intval($_POST['quantity']);
        
        $sql = "UPDATE emergency_kit SET 
                item_name = '$item_name',
                category = '$category',
                quantity = '$quantity',
                updated_at = NOW()
                WHERE item_id = '$item_id' AND user_id = '$user_id'";
        
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
        }
        mysqli_close($conn);
        exit();
    }
    
    // DELETE - Remove kit item
    if ($action === 'delete_kit_item') {
        $item_id = intval($_POST['item_id']);
        
        $sql = "DELETE FROM emergency_kit WHERE item_id = '$item_id' AND user_id = '$user_id'";
        
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
        }
        mysqli_close($conn);
        exit();
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Preparedness | ALERTO</title>
    <link rel="icon" type="image/png" href="images/browsericon.png">
    <link rel="stylesheet" href="css/prep.css">
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
                <li><a href="prep.php" class="nav-item active">My Preparedness</a></li>
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
                <div class="page-title">MY PREPAREDNESS</div>
            </div>
            <div class="logo-container">
                <a href="homepage.php">
                    <img src="images/logo.png" alt="Alerto Logo" class="logo">
                </a>
            </div>
        </div>
        <div class="header-line"></div>
    </header>

    <!-- Navigation Section -->
    <nav class="navigation-section">
        <div class="nav-buttons-left">
            <button class="nav-button active" id="contactsNav">MY EMERGENCY CONTACTS</button>
            <button class="nav-button" id="kitNav">MY EMERGENCY KIT BUILDER</button>
        </div>
        <button class="generate-pdf-nav-btn" id="generatePdfNavBtn">
            <img src="images/generate.jpg" alt="Generate PDF" class="pdf-icon">
            Generate PDF
        </button>
    </nav>

    <main class="preparedness-wrapper">
        <!-- Emergency Contacts Card -->
        <section class="card" id="contacts">
            <h2 class="card-title">MY EMERGENCY CONTACTS</h2>
            
            <div class="contacts-grid" id="contactsGrid">
                <!-- Contacts will be loaded here dynamically -->
            </div>
            
            <div class="card-actions">
                <button class="action-btn add-contact-btn" id="addContactBtn">
                    + Add New Contact
                </button>
                <button class="action-btn save-btn" id="saveBtn">
                    Save
                </button>
            </div>
        </section>

        <!-- Emergency Kit Builder Card -->
        <section class="card" id="kit">
            <h2 class="card-title">MY EMERGENCY KIT BUILDER</h2>
            
            <div class="kit-grid" id="kitGrid">
                <!-- Kit categories will be loaded here dynamically -->
            </div>
            
            <div class="card-actions">
                <button class="action-btn add-kit-btn" id="addCategoryBtn">
                    <span class="btn-icon">+</span>
                    ADD NEW CATEGORY
                </button>
                <button class="action-btn save-kit-btn" id="saveKitBtn">
                    Save
                </button>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; Alerto 2025. All Rights Reserved.</p>
    </footer>

    <script src="js/prep.js"></script>
</body>
</html>