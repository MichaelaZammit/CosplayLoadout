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

if (!$post) {
  echo "Post not found.";
  exit;
}

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

<div class="post-wrapper" style="display: flex; gap: 40px; align-items: flex-start;">

  <!-- LEFT: CHARACTER PREVIEW -->
  <div class="image-side">
    <div class="character-container" style="position: relative; width: 300px; height: 600px;">
      <img src="assets/<?= $post['gender'] === 'f' ? 'female_base.png' : 'male_base.png' ?>" class="character-layer" style="position: absolute; top: 0; left: 0; width: 100%;">
      <?php if ($post['top']): ?><img src="assets/clothes/<?= $post['top'] . $post['gender'] ?><?= $post['top_color'] ? "_{$post['top_color']}" : "" ?>.png" class="character-layer" style="position: absolute; top: 0; left: 0; width: 100%;"><?php endif; ?>
      <?php if ($post['pants']): ?><img src="assets/clothes/<?= $post['pants'] . $post['gender'] ?><?= $post['pants_color'] ? "_{$post['pants_color']}" : "" ?>.png" class="character-layer" style="position: absolute; top: 0; left: 0; width: 100%;"><?php endif; ?>
      <?php if ($post['shoes']): ?><img src="assets/clothes/<?= $post['shoes'] . $post['gender'] ?><?= $post['shoes_color'] ? "_{$post['shoes_color']}" : "" ?>.png" class="character-layer" style="position: absolute; top: 0; left: 0; width: 100%;"><?php endif; ?>
      <?php if ($post['reference_item']): ?><img src="assets/clothes/<?= $post['reference_item'] . $post['gender'] ?><?= $post['reference_color'] ? "_{$post['reference_color']}" : "" ?>.png" class="character-layer" style="position: absolute; top: 0; left: 0; width: 100%;"><?php endif; ?>
    </div>
  </div>

  <!-- RIGHT: TEXT + ACTIONS -->
  <div class="details-side">

    <div class="action-row">
      <form action="like_post.php" method="POST" class="icon-form">
        <input type="hidden" name="post_id" value="<?= $post['id']; ?>">
        <button type="submit" class="icon-button">❤️</button>
      </form>
      <span class="likes-count"><?= $like_count ?></span>

      <form action="repost.php" method="POST" class="icon-form">
        <input type="hidden" name="post_id" value="<?= $post['id']; ?>">
        <button type="submit" class="icon-button">🔁</button>
      </form>

      <form action="follow_user.php" method="POST" class="follow-form">
        <input type="hidden" name="follow_user_id" value="<?= $post['user_id']; ?>">
        <button type="submit" class="follow-button <?= $isFollowing ? 'following' : '' ?>">
          <?= $isFollowing ? '✔ Following' : '+ Follow' ?>
        </button>
      </form>
    </div>

    <h2 class="post-title"><?= htmlspecialchars($post['title']); ?></h2>
    <?php
      function makeLinksClickable($text) {
        $text = htmlspecialchars($text);
        $text = nl2br($text);
    
        // Replace plain links with anchor tags (surrounded by word boundaries or line breaks)
        return preg_replace_callback(
            '/(?<!href=")(https?:\/\/[^\s<]+)/',
            function ($matches) {
                $url = $matches[1];
                return '<a href="' . $url . '" target="_blank" rel="noopener noreferrer">' . $url . '</a>';
            },
            $text
        );
    }    
      ?>
      <p class="description"><?= makeLinksClickable($post['description']); ?></p>


    <?php if (!empty($post['images'])):
      $images = explode(',', $post['images']); ?>
      <div class="post-images" style="margin-top: 15px; display: flex; flex-wrap: wrap; gap: 10px;">
        <?php foreach ($images as $img): ?>
          <img src="uploads/<?= htmlspecialchars(trim($img)) ?>" alt="Additional Image" style="width: 100px; height: auto; border-radius: 6px;">
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="post-meta">
      <p>Posted by: <a href="profile.php?id=<?= $post['user_id']; ?>" class="user-link">@<?= htmlspecialchars($post['username']); ?></a></p>
      <p><?= htmlspecialchars($post['created_at']); ?></p>
    </div>

    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
      <form action="edit_post.php" method="GET" class="edit-post-form">
        <input type="hidden" name="id" value="<?= $post['id']; ?>">
        <button type="submit" class="follow-button">Edit Post</button>
      </form>
    <?php endif; ?>

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
const likeForm = document.querySelector('.icon-form');
if (likeForm) {
  likeForm.addEventListener('submit', function(e) {
    const button = likeForm.querySelector('.icon-button');
    button.classList.add('liked');
  });
}
</script>