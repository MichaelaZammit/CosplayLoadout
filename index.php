<?php
require 'includes/db.php';
include 'includes/header.php';

// Fetch posts and user info, excluding reposts
$stmt = $pdo->query("
  SELECT posts.*, users.username, users.profile_image 
  FROM posts 
  JOIN users ON posts.user_id = users.id 
  WHERE posts.repost_of IS NULL
  ORDER BY posts.created_at DESC
");
$posts = $stmt->fetchAll();
?>

<div class="home-container">
  <h2 class="page-title">Explore Creations</h2>
  <div class="grid-container">
    <?php foreach ($posts as $post): ?>
      <a href="moreinfo.php?id=<?= $post['id']; ?>" class="post-link">
        <div class="post-card">
          <div class="post-header" style="text-align: center;">
            <span class="username">@<?= htmlspecialchars($post['username']) ?></span>
          </div>

          <div class="character-container">
            <img src="uploads/<?= htmlspecialchars($post['image']) ?>" class="full-outfit" alt="Outfit">
          </div>

          <h3 class="post-title"><?= htmlspecialchars($post['title']) ?></h3>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</div>
