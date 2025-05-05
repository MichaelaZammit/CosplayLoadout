<?php
session_start();
require 'includes/db.php';
include 'includes/header.php';

// Auto-login via cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
}

// Redirect if not logged in and not viewing someone else
if (!isset($_SESSION['user_id']) && !isset($_GET['id'])) {
    header('Location: login.php');
    exit;
}

// Determine profile to show
$profile_id = $_GET['id'] ?? $_SESSION['user_id'];
$profile_id = intval($profile_id);

// Fetch user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$profile_id]);
$profile_user = $stmt->fetch();

// Fetch original posts
$post_stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? AND repost_of IS NULL ORDER BY created_at DESC");
$post_stmt->execute([$profile_id]);
$posts = $post_stmt->fetchAll();

// Fetch reposts
$repost_stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? AND repost_of IS NOT NULL ORDER BY created_at DESC");
$repost_stmt->execute([$profile_id]);
$reposts = $repost_stmt->fetchAll();

// Get follow stats
$followers_count = $pdo->prepare("SELECT COUNT(*) FROM followers WHERE following_id = ?");
$followers_count->execute([$profile_id]);
$followers_count = $followers_count->fetchColumn();

$following_count = $pdo->prepare("SELECT COUNT(*) FROM followers WHERE follower_id = ?");
$following_count->execute([$profile_id]);
$following_count = $following_count->fetchColumn();
?>

<!-- Profile Header -->
<div class="profile-info-container">
  <div class="profile-image">
    <img src="uploads/<?= htmlspecialchars($profile_user['profile_image'] ?? 'default.png') ?>" alt="Profile Image">
  </div>

  <div class="profile-meta">
    <div class="meta-top">
      <h2><?= htmlspecialchars($profile_user['username']) ?></h2>
      <?php if ($profile_id == $_SESSION['user_id']): ?>
        <div class="action-buttons">
          <a href="edit_profile.php" class="edit-btn">Edit</a>
          <button type="button" class="edit-btn delete-toggle-btn" onclick="toggleDeleteMode()">Delete Posts</button>
        </div>
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

<!-- ALL POSTS IN ONE FORM -->
<?php if ($profile_id == $_SESSION['user_id']): ?>
<form action="delete_selected_posts.php" method="POST" id="deleteForm">
<?php endif; ?>

<!-- Original Posts -->
<?php if (!empty($posts)): ?>
  <div class="page-title"><h2>Your Posts</h2></div>
  <div class="home-container">
    <div class="grid-container">
      <?php foreach ($posts as $post): ?>
        <?php if (!empty($post['image']) && file_exists("uploads/" . $post['image'])): ?>
          <div class="post-wrapper">
            <div class="post-card">
              <?php if ($profile_id == $_SESSION['user_id']): ?>
                <input type="checkbox" name="post_ids[]" value="<?= $post['id'] ?>" class="delete-checkbox" style="display:none;">
              <?php endif; ?>
              <a href="moreinfo.php?id=<?= $post['id']; ?>" class="post-link">
                <div class="character-container">
                  <img src="uploads/<?= htmlspecialchars($post['image']) ?>" class="full-outfit" alt="Outfit">
                </div>
                <h3 class="post-title"><?= htmlspecialchars($post['title'] ?? '') ?></h3>
              </a>
            </div>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>

<!-- Reposts -->
<?php if (!empty($reposts)): ?>
  <div class="page-title"><h2>Your Reposts</h2></div>
  <div class="home-container">
    <div class="grid-container">
      <?php foreach ($reposts as $repost): ?>
        <?php if (!empty($repost['image']) && file_exists("uploads/" . $repost['image'])): ?>
          <div class="post-wrapper">
            <div class="post-card">
              <?php if ($profile_id == $_SESSION['user_id']): ?>
                <input type="checkbox" name="post_ids[]" value="<?= $repost['id'] ?>" class="delete-checkbox" style="display:none;">
              <?php endif; ?>
              <a href="moreinfo.php?id=<?= $repost['id']; ?>" class="post-link">
                <div class="character-container">
                  <img src="uploads/<?= htmlspecialchars($repost['image']) ?>" class="full-outfit" alt="Outfit">
                </div>
                <h3 class="post-title">Repost: <?= htmlspecialchars($repost['title'] ?? '') ?></h3>
              </a>
            </div>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>

<?php if ($profile_id == $_SESSION['user_id']): ?>
  <div class="center-btn" style="text-align:center; display:none;" id="confirmDeleteWrapper">
    <button type="submit" class="delete-btn">Delete Selected</button>
  </div>
</form>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>

<!-- TOGGLE DELETE MODE SCRIPT -->
<script>
function toggleDeleteMode() {
  const checkboxes = document.querySelectorAll('.delete-checkbox');
  const deleteBtn = document.getElementById('confirmDeleteWrapper');
  const toggleBtn = document.querySelector('.delete-toggle-btn');

  checkboxes.forEach(cb => {
    cb.style.display = cb.style.display === 'none' ? 'block' : 'none';
  });

  if (deleteBtn) {
    deleteBtn.style.display = deleteBtn.style.display === 'none' ? 'block' : 'none';
  }

  toggleBtn.textContent = toggleBtn.textContent === 'Delete Posts' ? 'Cancel' : 'Delete Posts';
}
</script>
