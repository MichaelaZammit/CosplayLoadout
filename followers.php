<?php
require 'includes/db.php';
include 'includes/header.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

// Remove a follower (they followed you)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_id'])) {
    $remove_id = $_POST['remove_id'];
    $delete = $pdo->prepare("DELETE FROM followers WHERE user_id = ? AND follows_id = ?");
    $delete->execute([$remove_id, $user_id]);
    header("Location: followers.php");
    exit;
}

// Get list of users who follow YOU
$stmt = $pdo->prepare("
    SELECT users.id, users.username, users.profile_image
    FROM followers 
    JOIN users ON users.id = followers.user_id 
    WHERE followers.follows_id = ?
");
$stmt->execute([$user_id]);
$followers = $stmt->fetchAll();
?>

<div class="followers-container">
  <h2>Your Followers</h2>

  <?php if (count($followers) === 0): ?>
    <p>You don't have any followers yet 😢</p>
  <?php else: ?>
    <?php foreach ($followers as $follower): ?>
      <div class="follower-entry">
        <div class="follower-left">
          <img src="uploads/<?= htmlspecialchars($follower['profile_image'] ?? 'default.png') ?>" alt="Profile Image">
          <span><?= htmlspecialchars($follower['username']) ?></span>
        </div>
        <form method="POST">
          <input type="hidden" name="remove_id" value="<?= $follower['id'] ?>">
          <button type="submit" class="btn remove-btn">Remove</button>
        </form>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
