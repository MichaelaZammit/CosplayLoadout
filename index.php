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
      <?php
        $image = $post['image'] ?? '';
        $gender = str_contains($image, 'm') ? 'male' : 'female';
        $baseImage = ($gender === 'male') ? 'male_base.png' : 'female_base.png';

        preg_match('/top\d+/', $image, $topMatch);
        preg_match('/pants\d+/', $image, $bottomMatch);
        preg_match('/shoes\d+/', $image, $shoesMatch);

        $topFile = isset($topMatch[0]) ? $topMatch[0] . ($gender === 'male' ? 'm' : 'f') : '';
        $bottomFile = isset($bottomMatch[0]) ? $bottomMatch[0] . ($gender === 'male' ? 'm' : 'f') : '';
        $shoesFile = isset($shoesMatch[0]) ? $shoesMatch[0] . ($gender === 'male' ? 'm' : 'f') : '';
      ?>
      <a href="moreinfo.php?id=<?= $post['id']; ?>" class="post-link">
        <div class="post-card">
          <div class="post-header">
            <img src="uploads/<?= htmlspecialchars($post['profile_image'] ?? 'default.png') ?>" alt="User Icon" class="user-icon">
            <span class="username">@<?= htmlspecialchars($post['username']) ?></span>
          </div>

          <div class="character-container">
            <img src="Assets/<?= $baseImage ?>" class="layer base" alt="Base">
            <?php if (!empty($topFile)): ?>
              <img src="Assets/clothes/<?= $topFile ?>.png" class="layer top" alt="Top">
            <?php endif; ?>
            <?php if (!empty($bottomFile)): ?>
              <img src="Assets/clothes/<?= $bottomFile ?>.png" class="layer bottom" alt="Bottom">
            <?php endif; ?>
            <?php if (!empty($shoesFile)): ?>
              <img src="Assets/clothes/<?= $shoesFile ?>.png" class="layer shoes" alt="Shoes">
            <?php endif; ?>
          </div>

          <h3 class="post-title"><?= htmlspecialchars($post['title']) ?></h3>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</div>
