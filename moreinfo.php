<?php
include 'includes/header.php';
include 'includes/db.php';

if (!isset($_GET['id'])) {
    echo "Post not found.";
    exit;
}

$post_id = intval($_GET['id']);

// Fetch post details
$stmt = $pdo->prepare("SELECT posts.*, users.username, users.id AS user_id FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

// Fetch comments
$comments_stmt = $pdo->prepare("
    SELECT comments.*, users.username, users.id AS user_id
    FROM comments
    JOIN users ON comments.user_id = users.id
    WHERE comments.post_id = ?
    ORDER BY comments.created_at DESC
");
$comments_stmt->execute([$post_id]);
$comments = $comments_stmt->fetchAll();

// Fetch like count
$like_stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
$like_stmt->execute([$post_id]);
$like_count = $like_stmt->fetchColumn();

// Check if user already follows
$isFollowing = false;
if (isset($_SESSION['user_id'])) {
    $checkFollow = $pdo->prepare("SELECT * FROM followers WHERE follower_id = ? AND following_id = ?");
    $checkFollow->execute([$_SESSION['user_id'], $post['user_id']]);
    $isFollowing = $checkFollow->fetch() ? true : false;
}
?>

<div class="page-container">

<div class="post-wrapper">
  <!-- LEFT: Character + Actions -->
  <div class="character-side">
    <img src="uploads/<?= htmlspecialchars($post['image']); ?>" alt="Post Image" class="character-img">

    <div class="post-actions">
      <form action="like_post.php" method="POST" class="action-form">
        <input type="hidden" name="post_id" value="<?= $post['id']; ?>">
        <button type="submit" class="like-button">❤️</button>
      </form>
      <span class="likes-count"><?= $like_count ?></span>

      <form action="repost.php" method="POST" class="action-form">
        <input type="hidden" name="post_id" value="<?= $post['id']; ?>">
        <button type="submit" class="repost-button">🔁</button>
      </form>
    </div>
  </div>

  <!-- RIGHT: Info -->
  <div class="post-info">
    <form action="follow_user.php" method="POST" class="follow-form">
      <input type="hidden" name="follow_user_id" value="<?= $post['user_id']; ?>">
      <button type="submit" class="follow-button <?= $isFollowing ? 'following' : '' ?>">
        <?= $isFollowing ? '✔ Following' : '+ Follow' ?>
      </button>
    </form>

    <h2 class="post-title"><?= htmlspecialchars($post['title']); ?></h2>
    <p class="description"><?= nl2br(htmlspecialchars($post['description'])); ?></p>

    <div class="post-meta">
      <p>Posted by: <a href="profile.php?id=<?= $post['user_id']; ?>" class="user-link">@<?= htmlspecialchars($post['username']); ?></a></p>
      <p><?= htmlspecialchars($post['created_at']); ?></p>
    </div>
  </div>
</div>

<div class="comments-wrapper">
  <h3 class="comments-heading">Comments</h3>

  <div class="comments-list">
    <?php foreach ($comments as $comment): ?>
      <div class="comment">
        <span class="comment-user">@<?= htmlspecialchars($comment['username']) ?></span>
        <span class="comment-text"><?= nl2br(htmlspecialchars($comment['content'])) ?></span>
      </div>
    <?php endforeach; ?>
  </div>

  <?php if (isset($_SESSION['user_id'])): ?>
    <form method="POST" action="submit_comment.php" class="comment-form">
      <input type="hidden" name="post_id" value="<?= $post_id ?>">
      <div class="comment-input-wrapper">
        <textarea name="content" placeholder="Add a comment..." required></textarea>
        <button type="submit">Post</button>
      </div>
    </form>
  <?php else: ?>
    <p><a href="login.php">Log in</a> to comment.</p>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

<script>
// Like button animation
document.getElementById('like-form').addEventListener('submit', function(e) {
  var button = document.getElementById('like-button');
  button.classList.add('liked');
});
</script>
