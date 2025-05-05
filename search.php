<?php
session_start();
require 'includes/db.php';
include 'includes/header.php';

$query = $_GET['q'] ?? '';
$search = "%$query%";

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
        $image = $post['image'] ?? '';
        $gender = str_contains($image, 'm') ? 'male' : 'female';
        $baseImage = ($gender === 'male') ? 'male_base.png' : 'female_base.png';

        preg_match('/top\d+/', $image, $topMatch);
        preg_match('/pants\d+/', $image, $pantsMatch);
        preg_match('/shoes\d+/', $image, $shoesMatch);

        $suffix = ($gender === 'male') ? 'm' : 'f';
        $topFile = isset($topMatch[0]) ? $topMatch[0] . $suffix : '';
        $pantsFile = isset($pantsMatch[0]) ? $pantsMatch[0] . $suffix : '';
        $shoesFile = isset($shoesMatch[0]) ? $shoesMatch[0] . $suffix : '';
      ?>

      <a href="view.php?id=<?= $post['id']; ?>" class="post-link">
        <div class="post-card">
        <div class="post-header" style="text-align: center;">
  <span class="username">@<?= htmlspecialchars($post['username']) ?></span>
</div>

          <div class="character-container">
            <img src="Assets/<?= $baseImage ?>" class="layer base" alt="Base Body">
            <?php if (!empty($topFile)): ?>
              <img src="Assets/clothes/<?= $topFile ?>.png" class="layer top" alt="Top">
            <?php endif; ?>
            <?php if (!empty($pantsFile)): ?>
              <img src="Assets/clothes/<?= $pantsFile ?>.png" class="layer pants" alt="Pants">
            <?php endif; ?>
            <?php if (!empty($shoesFile)): ?>
              <img src="Assets/clothes/<?= $shoesFile ?>.png" class="layer shoes" alt="Shoes">
            <?php endif; ?>
          </div>

          <h3 class="post-title"><?= htmlspecialchars($post['title']) ?></h3>
        </div>
      </a>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
