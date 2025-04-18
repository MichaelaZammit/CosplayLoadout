<?php
session_start(); // Make sure session is started for login check
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cosplay Creator</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

<header>

        <!-- Navigation Bar -->
    <nav class="navbar">
    <!-- Left-aligned navigation links -->
    <div class="nav-left">
        <a href="index.php" class="nav-link">Home</a>
        <a href="create.php" class="nav-link">Create</a>
        <a href="profile.php" class="nav-link">Profile</a>
    </div>

    <!-- Right-aligned search form and auth buttons -->
    <div class="nav-right">
        <form class="search-form" action="search.php" method="GET">
        <input type="text" name="q" class="search-input" placeholder="Search..." />
        <button type="submit" class="search-btn">Search</button>
        </form>
        <a href="login.php" class="btn login-btn">Login</a>
        <a href="register.php" class="btn register-btn">Register</a>
        <!-- If user is logged in, you would show:
        <a href="logout.php" class="btn logout-btn">Logout</a> instead -->
    </div>
    </nav>

</header>

<main>
