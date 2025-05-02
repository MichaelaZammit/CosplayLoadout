<?php
require 'includes/db.php';
include 'includes/header.php';

$current_user_id = $_SESSION['user_id'] ?? null;
$profile_id = $_GET['id'] ?? $current_user_id;

if (!$profile_id) {
    header("Location: login.php");
    exit;
}

// Handle remove follower (only if viewing your own profile)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_id']) && $profile_id == $current_user_id) {
    $remove_id = $_POST['remove_id'];
    $delete = $pdo->prepare("DELETE FROM followers WHERE follower_id = ? AND following_id = ?");
    $delete->execute([$remove_id, $profile_id]);
    header("Location: followers.php?id=" . $profile_id);
    exit;
}

// Get profile user info
$user_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->execute([$profile_id]);
$profile_user = $user_stmt->fetch();

// Get followers of the profile
$stmt = $pdo->prepare("
    SELECT users.id, users.username, users.profile_image
    FROM followers
    JOIN users ON users.id = followers.follower_id
    WHERE followers.following_id = ?
");
$stmt->execute([$profile_id]);
$followers = $stmt->fetchAll();
?>

<div class="followers-container">
  <h2><?= htmlspecialchars($profile_user['username']) ?>'s Followers</h2>

  <?php if (empty($followers)): ?>
    <p>No followers yet.</p>
  <?php else: ?>
    <?php foreach ($followers as $follower): ?>
      <div class="follower-entry">
        <div class="follower-left">
          <img src="uploads/<?= htmlspecialchars($follower['profile_image'] ?? 'default.png') ?>" alt="Profile Image">
          <a href="user.php?id=<?= $follower['id'] ?>" class="user-link">
            <?= htmlspecialchars($follower['username']) ?>
          </a>
        </div>

        <?php if ($current_user_id == $profile_id): ?>
          <form method="POST">
            <input type="hidden" name="remove_id" value="<?= $follower['id'] ?>">
            <button type="submit" class="btn remove-btn">Remove</button>
          </form>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
