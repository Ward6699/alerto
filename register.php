<?php
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

// handle form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // check password match
    if ($password !== $confirm_password) {
        $message = "❌ Passwords do not match.";
    } else {
        // hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // check if email exists
        $check = $conn->query("SELECT * FROM users WHERE email='$email'");
        if ($check->num_rows > 0) {
            $message = "⚠️ Email already exists.";
        } else {
            // insert new user
            $sql = "INSERT INTO users (name, email, password) 
                    VALUES ('$fullname', '$email', '$hashed_password')";
            
            if ($conn->query($sql) === TRUE) {
                // redirect to login with success flag
                header("Location: signin.php?success=1");
                exit;
            } else {
                $message = "❌ Error: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up | ALERTO</title>
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

        <h2 class="register-title">SIGN UP</h2>

        <!-- show message if error -->
        <?php if (!empty($message)) { echo "<p style='color:red; font-weight:bold;'>$message</p>"; } ?>

        <form id="registrationForm" class="registration-form" method="POST" action="register.php">
          <div class="form-group">
            <label for="fullname">FULL NAME :</label>
            <input type="text" id="fullname" name="fullname" required>
          </div>

          <div class="form-group">
            <label for="email">EMAIL ADDRESS:</label>
            <input type="email" id="email" name="email" required>
          </div>

          <div class="form-group password-group">
            <label for="password">PASSWORD:</label>
            <input type="password" id="password" name="password" required>
            <img src="images/eyewithoutline.png" class="toggle-password" data-target="password" alt="Toggle Password">
          </div>

          <div class="form-group password-group">
            <label for="confirm_password">CONFIRM PASSWORD :</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <img src="images/eyewithoutline.png" class="toggle-password" data-target="confirm_password" alt="Toggle Password">
          </div>

          <button type="submit" class="register-btn">REGISTER</button>
        </form>

        <div class="login-link">
          <span>Already have an account?</span>
          <a href="signin.php" class="login-text">Log In Here</a>
        </div>
      </div>
    </div>
  </div>
  <script src="js/script.js"></script>
</body>
</html>
