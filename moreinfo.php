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
    <!-- Left: Post Image -->
    <div class="post-image">
      <img src="uploads/<?= htmlspecialchars($post['image']); ?>" alt="Post Image">
    </div>

    <!-- Right: Post Details + Actions -->
    <div class="post-info">
      <div class="post-actions">
        <form id="follow-form" action="follow_user.php" method="POST" class="action-form">
          <input type="hidden" name="follow_user_id" value="<?= $post['user_id']; ?>">
          <button type="submit" id="follow-button" class="follow-button <?= $isFollowing ? 'following' : '' ?>">
            <?= $isFollowing ? '✔ Following' : '➕' ?>
          </button>
        </form>

        <form id="like-form" action="like_post.php" method="POST" class="action-form">
          <input type="hidden" name="post_id" value="<?= $post['id']; ?>">
          <button type="submit" id="like-button" class="like-button">❤️</button>
        </form>

        <span class="likes-count" id="likes-count"><?= $like_count ?> Likes</span>

        <form action="repost.php" method="POST" class="action-form">
          <input type="hidden" name="post_id" value="<?= $post['id']; ?>">
          <button type="submit" class="repost-button">🔁</button>
        </form>
      </div>

      <h2><?= htmlspecialchars($post['title']); ?></h2>
      <p class="description"><?= nl2br(htmlspecialchars($post['description'])); ?></p>
      <div class="post-meta">
        <p>Posted by: 
        <a href="profile.php?id=<?= $post['user_id']; ?>" class="user-link">
        <strong>@<?= htmlspecialchars($post['username']); ?></strong>
        </a>
        </p>
        <p>Date: <?= htmlspecialchars($post['created_at']); ?></p>
      </div>
    </div>
  </div>

  <!-- Comments -->
  <div class="comments-wrapper">
    <div class="comments-section">
      <h2>Comments</h2>

      <?php foreach ($comments as $comment): ?>
        <div class="comment">
          <a href="profile.php?id=<?= $comment['user_id']; ?>" class="user-link">
            <strong>@<?= htmlspecialchars($comment['username']) ?></strong>
          </a>
          <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
        </div>
      <?php endforeach; ?>

      <hr class="comments-separator">

      <?php if (isset($_SESSION['user_id'])): ?>
        <form method="POST" action="submit_comment.php" class="comment-form">
          <textarea name="content" placeholder="Write a comment..." required></textarea>
          <input type="hidden" name="post_id" value="<?= $post_id ?>">
          <button type="submit">Post Comment</button>
        </form>
      <?php else: ?>
        <p><a href="login.php">Log in</a> to post a comment!</p>
      <?php endif; ?>
    </div>
  </div>

</div>

<?php include 'includes/footer.php'; ?>

<script>
// Like button animation
document.getElementById('like-form').addEventListener('submit', function(e) {
  var button = document.getElementById('like-button');
  button.classList.add('liked');
});
</script>
