<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Cosplay Creator</title>
  <link rel="stylesheet" href="/CosplayLoadout/css/base.css">
  <link rel="stylesheet" href="/CosplayLoadout/css/navbar.css">
<?php
if (isset($pageStyles)) {
    foreach ($pageStyles as $style) {
        echo "<link rel='stylesheet' href='/CosplayLoadout/css/{$style}.css'>
";
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
    <a href="login.php" class="btn">Login</a>
    <a href="register.php" class="btn">Register</a>
  </div>
</nav>
<main>
