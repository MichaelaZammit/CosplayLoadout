<?php
session_start();
require 'includes/db.php';
include 'includes/header.php';


$query = $_GET['q'] ?? '';
$search = "%$query%";

// Search posts
$post_stmt = $pdo->prepare("SELECT * FROM posts WHERE title LIKE ? OR description LIKE ? ORDER BY created_at DESC");
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
      <div class="post-card">
        <a href="view.php?id=<?= $post['id']; ?>">
          <img src="uploads/<?= htmlspecialchars($post['image']); ?>" alt="Outfit">
        </a>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
