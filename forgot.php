<?php
// Set timezone to match your location (Philippines)
date_default_timezone_set('Asia/Manila');

// PHPMailer imports must be at the top
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

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
$message_type = ""; // success or error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);

    // Check if email exists
    $check = $conn->query("SELECT * FROM users WHERE email='$email' LIMIT 1");

    if ($check->num_rows == 1) {
        // Generate secure random token
        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', strtotime('+5 minutes')); // Token valid for 5 minutes

        // Delete any existing tokens for this email
        $conn->query("DELETE FROM password_resets WHERE email='$email'");

        // Insert new token
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $token, $expires_at);
        $stmt->execute();

        // Send email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'smoochzaki@gmail.com'; // Your Gmail address
            $mail->Password   = 'pzzd rqcy diwc hxio';     // Your Google App Password (16 characters)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('smoochzaki@gmail.com', 'ALERTO Support');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request - ALERTO';
            
            $reset_link = "http://localhost/alerto_proj/reset_password.php?token=" . $token;
            
            $mail->Body = "
                <html>
                <body style='font-family: Arial, sans-serif;'>
                    <h2>Password Reset Request</h2>
                    <p>You requested to reset your password for your ALERTO account.</p>
                    <p>Click the link below to reset your password:</p>
                    <p><a href='$reset_link' style='background-color: #ff0000; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Reset Password</a></p>
                    <p>Or copy this link to your browser: $reset_link</p>
                    <p><strong>This link will expire in 5 minutes.</strong></p>
                    <p>If you didn't request this, please ignore this email.</p>
                    <hr>
                    <p style='color: #888; font-size: 12px;'>ALERTO - Disaster Alert System</p>
                </body>
                </html>
            ";

            $mail->AltBody = "You requested to reset your password. Visit this link: $reset_link (expires in 5 minutes)";

            $mail->send();
            $message = "✅ Password reset link has been sent to your email. Please check your inbox.";
            $message_type = "success";
        } catch (Exception $e) {
            $message = "❌ Failed to send email. Error: {$mail->ErrorInfo}";
            $message_type = "error";
        }
    } else {
        // For security, don't reveal if email exists or not
        $message = "✅ If that email exists, a password reset link has been sent.";
        $message_type = "success";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password | ALERTO</title>
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

        <h2 class="register-title">FORGOT PASSWORD</h2>

        <?php if (!empty($message)) { 
            $color = $message_type === "success" ? "green" : "red";
            echo "<div style='background-color: rgba(255,255,255,0.95); padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid $color; position: relative; z-index: 10;'>
                    <p style='color:$color; font-weight:bold; margin: 0;'>$message</p>
                  </div>"; 
        } ?>

        <form class="registration-form" method="POST" action="forgot.php">
          <div class="form-group">
            <label for="email">EMAIL ADDRESS:</label>
            <input type="email" id="email" name="email" required>
          </div>

          <button type="submit" class="register-btn">SEND RESET LINK</button>
        </form>

        <div class="login-link">
            <span>Remember your password?</span>
            <a href="signin.php" class="signin-button">Sign In Here</a>
        </div>
      </div>
    </div>
  </div>
  <script src="js/script.js"></script>
</body>
</html>