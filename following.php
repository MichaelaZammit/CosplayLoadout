<?php
require 'includes/db.php';
include 'includes/header.php';

session_start(); // Make sure session is started

$current_user_id = $_SESSION['user_id'] ?? null;
$profile_id = $_GET['id'] ?? $current_user_id;

if (!$profile_id) {
    header("Location: login.php");
    exit;
}

// Handle unfollow (only if you're viewing your own following list)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unfollow_id']) && $profile_id == $current_user_id) {
    $unfollow_id = $_POST['unfollow_id'];
    $delete = $pdo->prepare("DELETE FROM followers WHERE follower_id = ? AND following_id = ?");
    $delete->execute([$current_user_id, $unfollow_id]);
    header("Location: following.php?id=" . $current_user_id);
    exit;
}

// Get profile user info
$user_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->execute([$profile_id]);
$profile_user = $user_stmt->fetch();

// Get following list
$stmt = $pdo->prepare("
    SELECT users.id, users.username, users.profile_image
    FROM followers
    JOIN users ON users.id = followers.following_id
    WHERE followers.follower_id = ?
");
$stmt->execute([$profile_id]);
$following = $stmt->fetchAll();
?>

<div class="followers-container">
  <h2><?= htmlspecialchars($profile_user['username']) ?>'s Following</h2>

  <?php if (empty($following)): ?>
    <p>Not following anyone.</p>
  <?php else: ?>
    <?php foreach ($following as $user): ?>
      <div class="follower-entry">
        <div class="follower-left">
          <img src="uploads/<?= htmlspecialchars($user['profile_image'] ?? 'default.png') ?>" alt="Profile Image">
          <a href="profile.php?id=<?= $user['id'] ?>" class="user-link">
            <?= htmlspecialchars($user['username']) ?>
          </a>
        </div>

        <?php if ($current_user_id == $profile_id): ?>
          <form method="POST">
            <input type="hidden" name="unfollow_id" value="<?= $user['id'] ?>">
            <button type="submit" class="btn remove-btn">Unfollow</button>
          </form>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
