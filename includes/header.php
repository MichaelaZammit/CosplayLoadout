<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Cosplay Creator</title>
  <link rel="stylesheet" href="/css/base.css">
  <link rel="stylesheet" href="/css/navbar.css">
  <link rel="stylesheet" href="/css/cards.css">
  <link rel="stylesheet" href="/css/creator.css">
  <link rel="stylesheet" href="/css/layout.css">
  <link rel="stylesheet" href="/css/postdetails.css">
  <link rel="stylesheet" href="/css/profile.css">
  <link rel="stylesheet" href="/css/moreinfo.css">
  <link rel="stylesheet" href="/css/comment.css">
<?php
if (isset($pageStyles)) {
    foreach ($pageStyles as $style) {
        echo "<link rel='stylesheet' href='/css/{$style}.css'>\n";
    }
}
?>
</head>
<body>

<nav class="navbar">
  <div class="nav-left">
    <a href="index.php">Home</a>
    <a href="create.php">Create</a>
    <a href="profile.php">Profile</a>
  </div>
  <div class="nav-right">
    <form class="search-form" action="search.php" method="GET">
      <input type="text" name="q" class="search-input" placeholder="Search...">
      <button type="submit" class="search-btn">Search</button>
    </form>

    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="logout.php" class="btn logout-btn">Logout</a>
    <?php else: ?>
      <a href="login.php" class="btn">Login</a>
      <a href="register.php" class="btn">Register</a>
    <?php endif; ?>
    
  </div>
</nav>

<main>
