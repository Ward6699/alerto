<?php
// Set timezone to match your location (Philippines)
date_default_timezone_set('Asia/Manila');

// database connection
$host = "localhost"; 
$user = "u967494580_alerto";       // default in XAMPP/WAMP/MAMP
$pass = "Alerto!1028";           // leave empty unless you set a MySQL password
$dbname = "u967494580_alerto";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$valid_token = false;
$email = "";
$current_time = "";
$expires_at = "";
$time_remaining = "";

// Verify token from URL
if (isset($_GET['token'])) {
    $token = $conn->real_escape_string($_GET['token']);
    
    // Get current time
    $current_time = date('Y-m-d H:i:s');
    
    // Check if token exists and is not expired
    $result = $conn->query("SELECT email, expires_at FROM password_resets WHERE token='$token' LIMIT 1");
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $expires_at = $row['expires_at'];
        
        // Calculate time remaining
        $current_timestamp = strtotime($current_time);
        $expires_timestamp = strtotime($expires_at);
        $seconds_remaining = $expires_timestamp - $current_timestamp;
        
        if ($seconds_remaining > 0) {
            $minutes = floor($seconds_remaining / 60);
            $seconds = $seconds_remaining % 60;
            $time_remaining = $minutes . "m " . $seconds . "s";
            
            $valid_token = true;
            $email = $row['email'];
        } else {
            $message = "❌ This reset link has expired. Please request a new one.";
        }
    } else {
        $message = "❌ Invalid reset link. Please request a new one.";
    }
} else {
    $message = "❌ No reset token provided.";
}

// Handle password reset form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['token'])) {
    $token = $conn->real_escape_string($_POST['token']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $message = "❌ Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $message = "❌ Password must be at least 6 characters long.";
    } else {
        // Verify token again
        $current_time = date('Y-m-d H:i:s');
        $result = $conn->query("SELECT email FROM password_resets WHERE token='$token' AND expires_at > '$current_time' LIMIT 1");
        
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $email = $row['email'];
            
            // Hash new password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Update password
            $stmt = $conn->prepare("UPDATE users SET password=? WHERE email=?");
            $stmt->bind_param("ss", $hashed_password, $email);
            
            if ($stmt->execute()) {
                // Delete used token
                $conn->query("DELETE FROM password_resets WHERE token='$token'");
                
                $message = "✅ Password updated successfully! You can now <a href='signin.php' style='color: green; text-decoration: underline;'>sign in</a>.";
                $valid_token = false; // Hide form after success
            } else {
                $message = "❌ Error updating password. Please try again.";
            }
        } else {
            $message = "❌ Invalid or expired token.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password | ALERTO</title>
  <link rel="icon" type="image/png" href="images/browsericon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="container">
    <div class="registration-panel">
     
      <!-- Left Section -->
      <div class="left-section">
        <img src="images/ALERTO ELEMENT.png" alt="ALERTO" class="alerto-element">
        <img src="images/ALERT ELEMENTS.png" alt="Alert Elements" class="alert-elements">
      </div>

      <!-- Right Section -->
      <div class="right-section">
        <img src="images/PLANET.png" alt="Planet Background" class="planet-bg">

        <h2 class="register-title">RESET PASSWORD</h2>

        <?php if (!empty($message)) { 
            $color = strpos($message, '✅') !== false ? "green" : "red";
            echo "<div style='background-color: rgba(255,255,255,0.95); padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid $color; position: relative; z-index: 10;'>
                    <p style='color:$color; font-weight:bold; margin: 0;'>$message</p>
                  </div>"; 
        } ?>

        <?php if ($valid_token && !empty($expires_at)) { ?>
        <div style='background-color: rgba(255,255,255,0.95); padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid orange; position: relative; z-index: 10;'>
          <p style='color: #ff8c00; font-weight:bold; margin: 0;'>
            This reset link will expire at <?php echo date('g:i:s A', strtotime($expires_at)); ?>
          </p>
        </div>
        <?php } ?>

        <?php if ($valid_token) { ?>
        <form class="registration-form" method="POST" action="reset_password.php">
          <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
          
          <div class="form-group">
            <label>EMAIL:</label>
            <input type="text" value="<?php echo htmlspecialchars($email); ?>" disabled style="background-color: #f0f0f0;">
          </div>

          <div class="form-group password-group">
            <label for="password">NEW PASSWORD:</label>
            <input type="password" id="password" name="password" required minlength="6">
            <img src="images/eyewithoutline.png" class="toggle-password" data-target="password" alt="Toggle Password">
          </div>

          <div class="form-group password-group">
            <label for="confirm_password">CONFIRM PASSWORD:</label>
            <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
            <img src="images/eyewithoutline.png" class="toggle-password" data-target="confirm_password" alt="Toggle Password">
          </div>

          <button type="submit" class="register-btn">RESET PASSWORD</button>
        </form>
        <?php } ?>

        <div class="login-link">
          <a href="signin.php" class="signin-button">
            ← Back to Sign In
          </a>
        </div>
      </div>
    </div>
  </div>
  <script src="js/script.js"></script>
</body>
</html>