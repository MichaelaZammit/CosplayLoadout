<?php
session_start();
require 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
}

if (!isset($_SESSION['user_id']) && !isset($_GET['id'])) {
    header('Location: login.php');
    exit;
}

$profile_id = $_GET['id'] ?? $_SESSION['user_id'];
$profile_id = intval($profile_id);

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$profile_id]);
$profile_user = $stmt->fetch();

$post_stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? AND repost_of IS NULL ORDER BY created_at DESC");
$post_stmt->execute([$profile_id]);
$posts = $post_stmt->fetchAll();

$repost_stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? AND repost_of IS NOT NULL ORDER BY created_at DESC");
$repost_stmt->execute([$profile_id]);
$reposts = $repost_stmt->fetchAll();

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
          <a href="edit_profile.php" class="edit-btn">Edit Post</a>
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
            <div class="post-card">
              <?php if ($profile_id == $_SESSION['user_id']): ?>
                <input type="checkbox" name="post_ids[]" value="<?= $post['id'] ?>" class="delete-checkbox" style="display:none;">
              <?php endif; ?>
              <a href="moreinfo.php?id=<?= $post['id']; ?>" class="post-link">
                <div class="character-container" style="position: relative; width: 300px; height: 600px;">
                  <img src="assets/<?= $post['gender'] === 'f' ? 'female_base.png' : 'male_base.png' ?>" class="character-layer" style="position: absolute; top: 0; left: 0; width: 100%;">
                  <?php if ($post['top']): ?><img src="assets/clothes/<?= $post['top'] . $post['gender'] ?><?= $post['top_color'] ? "_{$post['top_color']}" : "" ?>.png" class="character-layer" style="position: absolute; top: 0; left: 0; width: 100%;"><?php endif; ?>
                  <?php if ($post['pants']): ?><img src="assets/clothes/<?= $post['pants'] . $post['gender'] ?><?= $post['pants_color'] ? "_{$post['pants_color']}" : "" ?>.png" class="character-layer" style="position: absolute; top: 0; left: 0; width: 100%;"><?php endif; ?>
                  <?php if ($post['shoes']): ?><img src="assets/clothes/<?= $post['shoes'] . $post['gender'] ?><?= $post['shoes_color'] ? "_{$post['shoes_color']}" : "" ?>.png" class="character-layer" style="position: absolute; top: 0; left: 0; width: 100%;"><?php endif; ?>
                  <?php if ($post['reference_item']): ?><img src="assets/clothes/<?= $post['reference_item'] . $post['gender'] ?><?= $post['reference_color'] ? "_{$post['reference_color']}" : "" ?>.png" class="character-layer" style="position: absolute; top: 0; left: 0; width: 100%;"><?php endif; ?>
                </div>
                <h3 class="post-title"><?= htmlspecialchars($post['title'] ?? '') ?></h3>
              </a>
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
            <div class="post-card">
              <?php if ($profile_id == $_SESSION['user_id']): ?>
                <input type="checkbox" name="post_ids[]" value="<?= $repost['id'] ?>" class="delete-checkbox" style="display:none;">
              <?php endif; ?>
              <a href="moreinfo.php?id=<?= $repost['id']; ?>" class="post-link">
                <div class="character-container" style="position: relative; width: 300px; height: 600px;">
                  <img src="assets/<?= $repost['gender'] === 'f' ? 'female_base.png' : 'male_base.png' ?>" class="character-layer" style="position: absolute; top: 0; left: 0; width: 100%;">
                  <?php if ($repost['top']): ?><img src="assets/clothes/<?= $repost['top'] . $repost['gender'] ?><?= $repost['top_color'] ? "_{$repost['top_color']}" : "" ?>.png" class="character-layer" style="position: absolute; top: 0; left: 0; width: 100%;"><?php endif; ?>
                  <?php if ($repost['pants']): ?><img src="assets/clothes/<?= $repost['pants'] . $repost['gender'] ?><?= $repost['pants_color'] ? "_{$repost['pants_color']}" : "" ?>.png" class="character-layer" style="position: absolute; top: 0; left: 0; width: 100%;"><?php endif; ?>
                  <?php if ($repost['shoes']): ?><img src="assets/clothes/<?= $repost['shoes'] . $repost['gender'] ?><?= $repost['shoes_color'] ? "_{$repost['shoes_color']}" : "" ?>.png" class="character-layer" style="position: absolute; top: 0; left: 0; width: 100%;"><?php endif; ?>
                  <?php if ($repost['reference_item']): ?><img src="assets/clothes/<?= $repost['reference_item'] . $repost['gender'] ?><?= $repost['reference_color'] ? "_{$repost['reference_color']}" : "" ?>.png" class="character-layer" style="position: absolute; top: 0; left: 0; width: 100%;"><?php endif; ?>
                </div>
                <h3 class="post-title">Repost: <?= htmlspecialchars($repost['title'] ?? '') ?></h3>
              </a>
            </div>

        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>

<?php if ($profile_id == $_SESSION['user_id']): ?>
  <div class="center-btn" style="text-align:center; display:none;" id="confirmDeleteWrapper">
    <button type="button" class="delete-btn" onclick="confirmDelete()">Delete Selected</button>
  </div>
</form>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>

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

function confirmDelete() {
  const checkboxes = document.querySelectorAll('.delete-checkbox:checked');
  if (checkboxes.length === 0) {
    alert("Please select at least one post to delete.");
    return;
  }

  const modal = document.getElementById("deleteConfirmModal");
  if (modal) {
    modal.style.display = "flex"; // This makes it a full-screen popup
  }
}

function closeDeleteModal() {
  const modal = document.getElementById("deleteConfirmModal");
  if (modal) {
    modal.style.display = "none";
  }
}

function submitDeleteForm() {
  document.getElementById("deleteForm").submit();
}

</script>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background-color:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
  <div style="background:white; padding:20px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.3); width:300px; text-align:center;">
    <h3>Are you sure?</h3>
    <p>This will permanently delete the selected cosplay post(s).</p>
    <div style="margin-top:15px;">
      <button type="button" onclick="submitDeleteForm()" style="margin-right:10px;">Yes, delete</button>
      <button type="button" onclick="closeDeleteModal()">Cancel</button>
    </div>
  </div>
</div>

