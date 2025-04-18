<?php
require 'includes/db.php';
include 'includes/header.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

// Unfollow a user (you follow them)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unfollow_id'])) {
    $unfollow_id = $_POST['unfollow_id'];
    $delete = $pdo->prepare("DELETE FROM followers WHERE user_id = ? AND follows_id = ?");
    $delete->execute([$user_id, $unfollow_id]);
    header("Location: following.php");
    exit;
}

// Get list of users YOU follow
$stmt = $pdo->prepare("
    SELECT users.id, users.username, users.profile_image
    FROM followers 
    JOIN users ON users.id = followers.follows_id 
    WHERE followers.user_id = ?
");
$stmt->execute([$user_id]);
$following = $stmt->fetchAll();
?>

<div class="followers-container">
  <h2>You’re Following</h2>

  <?php if (count($following) === 0): ?>
    <p>You're not following anyone yet 😅</p>
  <?php else: ?>
    <?php foreach ($following as $user): ?>
      <div class="follower-entry">
        <div class="follower-left">
          <img src="uploads/<?= htmlspecialchars($user['profile_image'] ?? 'default.png') ?>" alt="Profile Image">
          <span><?= htmlspecialchars($user['username']) ?></span>
        </div>
        <form method="POST">
          <input type="hidden" name="unfollow_id" value="<?= $user['id'] ?>">
          <button type="submit" class="btn remove-btn">Unfollow</button>
        </form>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
