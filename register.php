<?php
session_start();
require 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");

    try {
        $stmt->execute([$username, $email, $password]);
        $_SESSION['user_id'] = $pdo->lastInsertId();
        session_write_close();
        header("Location: edit_profile.php");
        exit;
    } catch (PDOException $e) {
        $error = "Username or email already exists.";
    }    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Cosplay Creator</title>
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>

<div class="auth-wrapper">
  <div class="auth-page-wrapper">
    <h2>Register</h2>

    <?php if (!empty($error)): ?>
      <div style="color: red; text-align: center; margin-bottom: 15px;">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <input type="text" name="username" placeholder="Username" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Register</button>
    </form>

    <div class="extra-link">
      Already have an account? <a href="login.php">Login here</a>
    </div>
  </div>
</div>

</body>
</html>
