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
    <a href="/index.php">Home</a>
    <a href="/create.php">Create</a>
    <a href="/profile.php">Profile</a>
    
    <div class="dropdown-wrapper">
      <span class="dropdown-title">Supplies</span>
      <div class="dropdown-list">
        <a href="/supplies/accessories.php">Accessories</a>
        <a href="/supplies/contact_lenses.php">Contact Lenses</a>
        <a href="/supplies/charity_shops.php">Charity Shops</a>
        <a href="/supplies/corsets.php">Corsets</a>
        <a href="/supplies/dancewear_leotards.php">Dancewear & Leotards</a>
        <a href="/supplies/electronics_lighting.php">Electronics & Lighting</a>
        <a href="/supplies/fabric_notions.php">Fabric & Notions</a>
        <a href="/supplies/fiberglass_fabrication.php">Fiberglass & Fabrication</a>
        <a href="/supplies/foam_pvc.php">Foam & PVC</a>
        <a href="/supplies/jewelry_making.php">Jewelry-making</a>
        <a href="/supplies/leatherworking.php">Leatherworking</a>
        <a href="/supplies/miscellaneous.php">Miscellaneous</a>
        <a href="/supplies/paints_dyes.php">Paints & Dyes</a>
        <a href="/supplies/patterns.php">Patterns</a>
        <a href="/supplies/makeup.php">Makeup</a>
        <a href="/supplies/power_tools_equipment.php">Power Tools & Equipment</a>
        <a href="/supplies/prosthetics.php">Prosthetics</a>
        <a href="/supplies/sculpting_casting.php">Sculpting & Casting</a>
        <a href="/supplies/shoes.php">Shoes</a>
        <a href="/supplies/thermoplastics_fosshape.php">Thermoplastics & Fosshape</a>
        <a href="/supplies/tights_socks.php">Tights & Socks</a>
        <a href="/supplies/trims.php">Trims</a>
        <a href="/supplies/wigs_hair.php">Wigs & Hair</a>
      </div>
    </div>
  </div>

  <div class="nav-right">
    <form class="search-form" action="/search.php" method="GET">
      <input type="text" name="q" class="search-input" placeholder="Search...">
      <button type="submit" class="search-btn">Search</button>
    </form>

    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="/logout.php" class="btn logout-btn">Logout</a>
    <?php else: ?>
      <a href="/login.php" class="btn">Login</a>
      <a href="/register.php" class="btn">Register</a>
    <?php endif; ?>
  </div>
</nav>


    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="logout.php" class="btn logout-btn">Logout</a>
    <?php else: ?>
      <a href="login.php" class="btn">Login</a>
      <a href="register.php" class="btn">Register</a>
    <?php endif; ?>
    
  </div>
</nav>

<main>
