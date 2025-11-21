<?php
session_start();

// database connection
$host = "localhost"; 
$user = "u967494580_alerto";       // default in XAMPP/WAMP/MAMP
$pass = "Alerto!1028";           // leave empty unless you set a MySQL password
$dbname = "u967494580_alerto";

$conn = new mysqli($host, $user, $pass, $dbname);

// check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// handle login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // check if email exists
    $sql = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // verify hashed password
        if (password_verify($password, $user['password'])) {
            // login success - create session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] == 'admin') {
                header("Location: admin/admin_dashboard.php"); // redirect admin to dashboard
            } else {
                header("Location: homepage.php"); // redirect regular user to homepage
            }
            exit();
        } else {
            $message = "❌ Invalid password.";
        }
    } else {
        $message = "⚠️ No account found with that email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In | ALERTO</title>
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

        <h2 class="register-title">SIGN IN</h2>

        <!-- message display -->
        <?php if (!empty($message)) { echo "<p style='color:red; font-weight:bold;'>$message</p>"; } ?>

        <form class="registration-form" method="POST" action="signin.php">
          <div class="form-group">
            <label for="email">EMAIL ADDRESS:</label>
            <input type="email" id="email" name="email" required>
          </div>

          <div class="form-group password-group">
            <label for="password">PASSWORD:</label>
            <input type="password" id="password" name="password" required>
            <img src="images/eyewithoutline.png" class="toggle-password" data-target="password" alt="Toggle Password">
          </div>

          <button type="submit" class="register-btn">SIGN IN</button>
        </form>

        <div class="forgot-password-link">
          <span>Forgot password? </span>
          <a href="forgot.php" class="login-text">Click here</a>
        </div>

        <div class="login-link">
          <span>Don't have an account?</span>
          <a href="register.php" class="login-text">Register Here</a>
        </div>
      </div>
    </div>
  </div>
  <script src="js/script.js"></script>
</body>
</html>