<?php
session_start();
require 'includes/db.php';
include 'includes/header.php';

$query = $_GET['q'] ?? '';
$search = "%$query%";

// Helper to get layered image paths
function imagePath($item, $gender, $color) {
  $base = "assets/clothes/";
  if (!$item) return null;

  if ($color) {
    $coloredPath = "{$base}{$item}{$gender}_{$color}.png";
    if (file_exists($coloredPath)) return $coloredPath;
  }

  $fallback = "{$base}{$item}{$gender}.png";
  return file_exists($fallback) ? $fallback : null;
}

// Search posts
$post_stmt = $pdo->prepare("
  SELECT posts.*, users.username, users.profile_image 
  FROM posts
  JOIN users ON posts.user_id = users.id
  WHERE (posts.title LIKE ? OR posts.description LIKE ?)
  ORDER BY posts.created_at DESC
");
$post_stmt->execute([$search, $search]);
$posts = $post_stmt->fetchAll();

// Search users
$user_stmt = $pdo->prepare("SELECT * FROM users WHERE username LIKE ? ORDER BY username ASC");
$user_stmt->execute([$search]);
$users = $user_stmt->fetchAll();
?>

<div class="page-title">
  <h2>Search Results for "<?= htmlspecialchars($query) ?>"</h2>
</div>

<!-- USER RESULTS -->
<?php if (count($users) > 0): ?>
  <h3 style="text-align:center;">Users</h3>
  <div class="followers-container">
    <?php foreach ($users as $user): ?>
      <div class="follower-entry">
        <div class="follower-left">
          <img src="uploads/<?= htmlspecialchars($user['profile_image'] ?? 'default.png') ?>" alt="Profile Image">
          <a href="user.php?id=<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<!-- POST RESULTS -->
<h3 style="text-align:center; margin-top: 40px;">Posts</h3>
<div class="grid-container">
  <?php if (count($posts) === 0 && count($users) === 0): ?>
    <p style="text-align:center;">No results found 🫤</p>
  <?php else: ?>
    <?php foreach ($posts as $post): ?>
      <?php
        $gender = $post['gender'] ?? 'm';
        $suffix = $gender === 'f' ? 'f' : 'm';
        $baseImage = $gender === 'f' ? 'female_base.png' : 'male_base.png';

        $topPath = imagePath($post['top'], $suffix, $post['top_color']);
        $pantsPath = imagePath($post['pants'], $suffix, $post['pants_color']);
        $shoesPath = imagePath($post['shoes'], $suffix, $post['shoes_color']);
        $refPath = imagePath($post['reference_item'], $suffix, $post['reference_color']);
      ?>

      <a href="moreinfo.php?id=<?= $post['id']; ?>" class="post-link">
        <div class="post-card">
          <div class="post-header" style="text-align: center;">
            <span class="username">@<?= htmlspecialchars($post['username']) ?></span>
          </div>

          <div class="character-container" style="position: relative; width: 100%; aspect-ratio: 1/1;">
            <img src="assets/<?= $baseImage ?>" class="layer base" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain;">
            <?php if ($topPath): ?>
              <img src="<?= $topPath ?>" class="layer top" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain;">
            <?php endif; ?>
            <?php if ($pantsPath): ?>
              <img src="<?= $pantsPath ?>" class="layer pants" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain;">
            <?php endif; ?>
            <?php if ($shoesPath): ?>
              <img src="<?= $shoesPath ?>" class="layer shoes" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain;">
            <?php endif; ?>
            <?php if ($refPath): ?>
              <img src="<?= $refPath ?>" class="layer reference" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain;">
            <?php endif; ?>
          </div>

          <h3 class="post-title"><?= htmlspecialchars($post['title']) ?></h3>
        </div>
      </a>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>