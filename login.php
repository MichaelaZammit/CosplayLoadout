<?php
session_start();
require 'includes/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];

        // Remember me: create a cookie
        if (isset($_POST['remember'])) {
            setcookie('user_id', $user['id'], time() + (86400 * 30), "/"); // 30 days
        }

        header("Location: profile.php");
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Cosplay Creator</title>
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>

<div class="auth-wrapper">
  <div class="auth-page-wrapper">
    <h2>Login</h2>

    <?php if (!empty($error)): ?>
      <div class="error-message">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>

      <div class="remember-me">
        <label>
          <input type="checkbox" name="remember">
          Remember Me
        </label>
      </div>

      <button type="submit">Login</button>
    </form>

    <div class="extra-link">
      Don't have an account? <a href="register.php">Register here</a>
    </div>
  </div>
</div>

</body>
</html>
