<?php
session_start();
require 'includes/db.php';
include 'includes/header.php';

// If session not set but cookie is there
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
}
// Allow access if user is logged in OR viewing someone else's profile
if (!isset($_SESSION['user_id']) && !isset($_GET['id'])) {
  header('Location: login.php');
  exit;
}

// Determine which user profile to show
if (isset($_GET['id'])) {
    $profile_id = intval($_GET['id']); // Viewing someone else's profile
} else {
    $profile_id = $_SESSION['user_id']; // Viewing own profile
}

// Fetch profile user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$profile_id]);
$profile_user = $stmt->fetch();

// Fetch original posts (not reposts)
$post_stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? AND repost_of IS NULL ORDER BY created_at DESC");
$post_stmt->execute([$profile_id]);
$posts = $post_stmt->fetchAll();

// Fetch reposts only
$repost_stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? AND repost_of IS NOT NULL ORDER BY created_at DESC");
$repost_stmt->execute([$profile_id]);
$reposts = $repost_stmt->fetchAll();

// Get followers and following counts
$followers_stmt = $pdo->prepare("SELECT COUNT(*) FROM followers WHERE following_id = ?");
$followers_stmt->execute([$profile_id]);
$followers_count = $followers_stmt->fetchColumn();

$following_stmt = $pdo->prepare("SELECT COUNT(*) FROM followers WHERE follower_id = ?");
$following_stmt->execute([$profile_id]);
$following_count = $following_stmt->fetchColumn();
?>

<!-- Profile Header Section -->
<div class="profile-info-container">
  <div class="profile-image">
    <img src="uploads/<?= htmlspecialchars($profile_user['profile_image'] ?? 'default.png') ?>" alt="Profile Image">
  </div>

  <div class="profile-meta">
    <div class="meta-top">
      <h2><?= htmlspecialchars($profile_user['username']) ?></h2>

      <?php if ($profile_id == $_SESSION['user_id']): ?>
        <a href="edit_profile.php" class="edit-btn">Edit</a>
      <?php endif; ?>
    </div>

    <div class="profile-stats">
  <a href="following.php?id=<?= $profile_id ?>" class="stat-link">
    <strong><?= $following_count ?></strong> Following
  </a>
  <a href="followers.php?id=<?= $profile_id ?>" class="stat-link">
    <strong><?= $followers_count ?></strong> Followers
  </a>
</div>

    <p class="profile-bio"><?= nl2br(htmlspecialchars($profile_user['bio'] ?? '')) ?></p>
  </div>
</div>

<!-- Your Creations Section -->
<div class="home-container">
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
            <img src="uploads/<?= htmlspecialchars($profile_user['profile_image'] ?? 'default.png') ?>" alt="User Icon" class="user-icon">
            <span class="username">@<?= htmlspecialchars($profile_user['username']) ?></span>
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

          <h3 class="post-title"><?= htmlspecialchars($post['title'] ?? '') ?></h3>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<!-- Your Reposts Section -->
<?php if (!empty($reposts)): ?>
<div class="page-title">
  <h2>Your Reposts</h2>
</div>
<div class="home-container">
  <div class="grid-container">
      <?php foreach ($reposts as $repost): ?>
      <?php
        $image = $repost['image'] ?? '';
        $gender = str_contains($image, 'm') ? 'male' : 'female';
        $baseImage = ($gender === 'male') ? 'male_base.png' : 'female_base.png';

        preg_match('/top\d+/', $image, $topMatch);
        preg_match('/pants\d+/', $image, $bottomMatch);
        preg_match('/shoes\d+/', $image, $shoesMatch);

        $topFile = isset($topMatch[0]) ? $topMatch[0] . ($gender === 'male' ? 'm' : 'f') : '';
        $bottomFile = isset($bottomMatch[0]) ? $bottomMatch[0] . ($gender === 'male' ? 'm' : 'f') : '';
        $shoesFile = isset($shoesMatch[0]) ? $shoesMatch[0] . ($gender === 'male' ? 'm' : 'f') : '';
      ?>
      <a href="moreinfo.php?id=<?= $repost['id']; ?>" class="post-link">
        <div class="post-card">
          <div class="post-header">
            <img src="uploads/<?= htmlspecialchars($profile_user['profile_image'] ?? 'default.png') ?>" alt="User Icon" class="user-icon">
            <span class="username">@<?= htmlspecialchars($profile_user['username']) ?></span>
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

          <h3 class="post-title"><?= htmlspecialchars($post['title'] ?? '') ?></h3>
        </div>
      </a>
      <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>