<?php
require 'includes/db.php';
include 'includes/header.php';

// Fetch all original posts (excluding reposts)
$stmt = $pdo->query("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE posts.repost_of IS NULL AND posts.is_deleted = 0 ORDER BY posts.created_at DESC");
$posts = $stmt->fetchAll();
?>

<div class="home-container">
  <h2 class="page-title">Explore Creations</h2>
  <div class="grid-container">
    <?php foreach ($posts as $post): ?>
      <div class="post-card">
        <a href="moreinfo.php?id=<?= $post['id']; ?>" class="post-link">
          <div class="post-header">
            <span class="username">@<?= htmlspecialchars($post['username']) ?></span>
          </div>

          <div class="character-container" style="position: relative; width: 300px; height: 600px;">
            <img src="assets/<?= $post['gender'] === 'f' ? 'female_base.png' : 'male_base.png' ?>" class="character-layer" style="position: absolute; top: 0; left: 0; width: 100%;">

            <?php if (!empty($post['top'])): ?>
              <img src="assets/clothes/<?= $post['top'] . $post['gender'] ?><?= $post['top_color'] ? '_'.$post['top_color'] : '' ?>.png" class="character-layer" style="position: absolute; top: 0; left: 0; width: 100%;">
            <?php endif; ?>

            <?php if (!empty($post['pants'])): ?>
              <img src="assets/clothes/<?= $post['pants'] . $post['gender'] ?><?= $post['pants_color'] ? '_'.$post['pants_color'] : '' ?>.png" class="character-layer" style="position: absolute; top: 0; left: 0; width: 100%;">
            <?php endif; ?>

            <?php if (!empty($post['shoes'])): ?>
              <img src="assets/clothes/<?= $post['shoes'] . $post['gender'] ?><?= $post['shoes_color'] ? '_'.$post['shoes_color'] : '' ?>.png" class="character-layer" style="position: absolute; top: 0; left: 0; width: 100%;">
            <?php endif; ?>

            <?php if (!empty($post['reference_item'])): ?>
              <img src="assets/clothes/<?= $post['reference_item'] . $post['gender'] ?><?= $post['reference_color'] ? '_'.$post['reference_color'] : '' ?>.png" class="character-layer" style="position: absolute; top: 0; left: 0; width: 100%;">
            <?php endif; ?>
          </div>

          <h3 class="post-title"><?= htmlspecialchars($post['title'] ?? 'Untitled') ?></h3>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
