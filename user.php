<?php
session_start();
require 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$viewer_id = $_SESSION['user_id'];
$profile_id = $_GET['id'] ?? null;

if (!$profile_id || $profile_id == $viewer_id) {
    header("Location: profile.php");
    exit;
}

// Fetch profile user's data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$profile_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "<p>User not found.</p>";
    include 'includes/footer.php';
    exit;
}

// Count followers
$follower_stmt = $pdo->prepare("SELECT COUNT(*) FROM followers WHERE following_id = ?");
$follower_stmt->execute([$profile_id]);
$follower_count = $follower_stmt->fetchColumn();

// Count following
$following_stmt = $pdo->prepare("SELECT COUNT(*) FROM followers WHERE follower_id = ?");
$following_stmt->execute([$profile_id]);
$following_count = $following_stmt->fetchColumn();

// Check if viewer is already following this user
$check_follow = $pdo->prepare("SELECT * FROM followers WHERE follower_id = ? AND following_id = ?");
$check_follow->execute([$viewer_id, $profile_id]);
$is_following = $check_follow->rowCount() > 0;

// Handle follow/unfollow
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['follow_id']) && $_POST['follow_id'] != $viewer_id) {
        if (!$is_following) {
            $follow = $pdo->prepare("INSERT INTO followers (follower_id, following_id) VALUES (?, ?)");
            $follow->execute([$viewer_id, $profile_id]);
            header("Location: user.php?id=$profile_id");
            exit;
        }
    }

    if (isset($_POST['unfollow_id']) && $_POST['unfollow_id'] != $viewer_id) {
        $unfollow = $pdo->prepare("DELETE FROM followers WHERE follower_id = ? AND following_id = ?");
        $unfollow->execute([$viewer_id, $profile_id]);
        header("Location: user.php?id=$profile_id");
        exit;
    }
}

// Get posts from this user and include user data
$post_stmt = $pdo->prepare("
  SELECT posts.*, users.username, users.profile_image 
  FROM posts 
  JOIN users ON posts.user_id = users.id 
  WHERE posts.user_id = ? AND posts.repost_of IS NULL 
  ORDER BY posts.created_at DESC
");
$post_stmt->execute([$profile_id]);
$posts = $post_stmt->fetchAll();

?>

<!-- Profile Header -->
<div class="profile-info-container">
  <div class="profile-image">
    <img src="uploads/<?= htmlspecialchars($user['profile_image'] ?? 'default.png') ?>" alt="Profile Image">
  </div>

  <div class="profile-meta">
    <div class="meta-top">
      <h2><?= htmlspecialchars($user['username']) ?></h2>

      <!-- Follow/Unfollow button -->
      <?php if ($viewer_id !== $profile_id): ?>
        <form method="POST">
          <?php if ($is_following): ?>
            <input type="hidden" name="unfollow_id" value="<?= $user['id'] ?>">
            <button type="submit" class="btn follow-btn" style="background:#ff4d4d; color:white;">Unfollow</button>
          <?php else: ?>
            <input type="hidden" name="follow_id" value="<?= $user['id'] ?>">
            <button type="submit" class="btn follow-btn">Follow</button>
          <?php endif; ?>
        </form>
      <?php endif; ?>
    </div>

    <div class="profile-stats">
      <a href="following.php?id=<?= $user['id'] ?>" class="stat-link"><strong><?= $following_count ?></strong> Following</a>
      <a href="followers.php?id=<?= $user['id'] ?>" class="stat-link"><strong><?= $follower_count ?></strong> Followers</a>
    </div>

    <p class="profile-bio"><?= nl2br(htmlspecialchars($user['bio'] ?? '')) ?></p>
  </div>
</div>

<!-- Posts Section -->
<div class="page-title">
  <h2><?= htmlspecialchars($user['username']) ?>'s Creations</h2>
</div>

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

<?php include 'includes/footer.php'; ?>
