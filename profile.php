<?php
require 'includes/db.php';
include 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Determine which user profile to show
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']); // Visiting another user's profile
} else {
    $user_id = $_SESSION['user_id']; // Viewing own profile
}

// Fetch user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Get followers and following counts
$followers_stmt = $pdo->prepare("SELECT COUNT(*) FROM followers WHERE following_id = ?");
$followers_stmt->execute([$user_id]);
$followers_count = $followers_stmt->fetchColumn();

$following_stmt = $pdo->prepare("SELECT COUNT(*) FROM followers WHERE follower_id = ?");
$following_stmt->execute([$user_id]);
$following_count = $following_stmt->fetchColumn();

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

      <!-- Only show Edit button if it's YOUR profile -->
      <?php if ($user['id'] == $_SESSION['user_id']): ?>
        <a href="edit_profile.php" class="edit-btn">Edit</a>
      <?php endif; ?>
    </div>

    <div class="profile-stats">
      <span><strong><?= $following_count ?></strong> Following</span>
      <span><strong><?= $followers_count ?></strong> Followers</span>
    </div>

    <p class="profile-bio"><?= nl2br(htmlspecialchars($user['bio'] ?? '')) ?></p>
  </div>
</div>

<!-- User's Posts Section -->
<div class="page-title">
  <h2><?= ($user['id'] == $_SESSION['user_id']) ? "Your Creations" : $user['username'] . "'s Creations" ?></h2>
</div>

<div class="grid-container">
  <?php foreach ($posts as $post): ?>
    <div class="post-card">
      <a href="moreinfo.php?id=<?= $post['id']; ?>">
        <img src="uploads/<?= htmlspecialchars($post['image']); ?>" alt="Outfit">
      </a>
    </div>
  <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>
