<?php
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

// Fetch user profile info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$profile_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "<p>User not found.</p>";
    include 'includes/footer.php';
    exit;
}

// Handle follow request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['follow_id'])) {
    $follow_id = (int) $_POST['follow_id'];

    if ($follow_id !== $viewer_id) {
        $check = $pdo->prepare("SELECT * FROM followers WHERE user_id = ? AND follows_id = ?");
        $check->execute([$viewer_id, $follow_id]);

        if ($check->rowCount() === 0) {
            $follow = $pdo->prepare("INSERT INTO followers (user_id, follows_id) VALUES (?, ?)");
            $follow->execute([$viewer_id, $follow_id]);
        }
    }
}

// Handle unfollow request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unfollow_id'])) {
    $unfollow_id = (int) $_POST['unfollow_id'];
    $unfollow = $pdo->prepare("DELETE FROM followers WHERE user_id = ? AND follows_id = ?");
    $unfollow->execute([$viewer_id, $unfollow_id]);
}

// Check if you're following them
$check_follow = $pdo->prepare("SELECT * FROM followers WHERE user_id = ? AND follows_id = ?");
$check_follow->execute([$viewer_id, $profile_id]);
$is_following = $check_follow->rowCount() > 0;

// Get user's posts
$post_stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$post_stmt->execute([$profile_id]);
$posts = $post_stmt->fetchAll();
?>

<!-- Profile View -->
<div class="profile-info-container">
  <div class="profile-image">
    <img src="uploads/<?= htmlspecialchars($user['profile_image'] ?? 'default.png') ?>" alt="Profile Image">
  </div>

  <div class="profile-meta">
    <div class="meta-top">
      <h2><?= htmlspecialchars($user['username']) ?></h2>

      <form method="POST">
        <?php if ($is_following): ?>
          <input type="hidden" name="unfollow_id" value="<?= $user['id'] ?>">
          <button type="submit" class="btn follow-btn" style="background:#ff4d4d;color:white;">Unfollow</button>
        <?php else: ?>
          <input type="hidden" name="follow_id" value="<?= $user['id'] ?>">
          <button type="submit" class="btn follow-btn">Follow</button>
        <?php endif; ?>
      </form>
    </div>

    <p class="profile-bio"><?= nl2br(htmlspecialchars($user['bio'] ?? '')) ?></p>
  </div>
</div>

<div class="page-title">
  <h2><?= htmlspecialchars($user['username']) ?>'s Creations</h2>
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
