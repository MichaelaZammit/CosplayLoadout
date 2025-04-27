<?php
require 'includes/db.php';
include 'includes/header.php';

// use $pdo, not $conn
$stmt = $pdo->query("SELECT posts.*, users.username, users.profile_image FROM posts JOIN users ON posts.user_id = users.id ORDER BY created_at DESC");
$posts = $stmt->fetchAll();
?>

<div class="home-container">
  <h2 class="page-title">Explore Creations</h2>
  <div class="grid-container">
    <?php foreach ($posts as $post): ?>
      <!-- Start Link -->
      <a href="moreinfo.php?id=<?= $post['id']; ?>" class="post-link">
        <div class="post-card">
          <div class="post-header">
            <img src="uploads/<?= htmlspecialchars($post['profile_image']) ?>" alt="User" class="user-icon">
            <span class="username">@<?= htmlspecialchars($post['username']) ?></span>
          </div>
          <img src="uploads/<?= htmlspecialchars($post['image']) ?>" alt="Outfit Image" class="outfit-preview">
          <h3 class="post-title"><?= htmlspecialchars($post['title']) ?></h3>
        </div>
      </a>
      <!-- End Link -->
    <?php endforeach; ?>
  </div>
</div>
