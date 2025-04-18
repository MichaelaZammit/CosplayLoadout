<?php
require 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Get followers and following count
$followers = $pdo->prepare("SELECT COUNT(*) FROM followers WHERE follows_id = ?");
$followers->execute([$user_id]);
$followers_count = $followers->fetchColumn();

$following = $pdo->prepare("SELECT COUNT(*) FROM followers WHERE user_id = ?");
$following->execute([$user_id]);
$following_count = $following->fetchColumn();

// Fetch user's posts
$post_stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$post_stmt->execute([$user_id]);
$posts = $post_stmt->fetchAll();
?>

<!-- Profile Header Section -->
<div class="profile-info-container">
  <!-- LEFT: Profile Image -->
  <div class="profile-image">
    <img src="uploads/<?= htmlspecialchars($user['profile_image'] ?? 'default.png') ?>" alt="Profile Image">
  </div>

  <!-- RIGHT: User Info -->
  <div class="profile-meta">
    <div class="meta-top">
      <h2><?= htmlspecialchars($user['username']) ?></h2>
      <a href="edit_profile.php" class="edit-btn">Edit</a>
    </div>

    <div class="profile-stats">
      <a href="following.php" class="stat-link"><strong><?= $following_count ?></strong> Following</a>
      <a href="followers.php" class="stat-link"><strong><?= $followers_count ?></strong> Followers</a>
    </div>

    <p class="profile-bio"><?= nl2br(htmlspecialchars($user['bio'] ?? '')) ?></p>
  </div>
</div>

<!-- Posts Section -->
<div class="page-title">
  <h2>Your Creations</h2>
</div>

<div class="grid-container">
  <?php foreach ($posts as $post): ?>
    <div class="post-card">
      <a href="view.php?id=<?= $post['id']; ?>">
        <img src="uploads/<?= htmlspecialchars($post['image']); ?>" alt="Outfit">
      </a>
    </div>
  <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>
