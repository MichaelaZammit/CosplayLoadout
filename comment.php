<?php
include 'includes/header.php';
include 'includes/db.php';

if (!isset($_GET['id'])) {
    echo "Post not found.";
    exit;
}

$post_id = intval($_GET['id']);

// Fetch post details
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

// Fetch comments with usernames
$comments_stmt = $conn->prepare("
    SELECT comments.*, users.username 
    FROM comments 
    JOIN users ON comments.user_id = users.id 
    WHERE comments.post_id = ? 
    ORDER BY comments.created_at DESC
");
$comments_stmt->execute([$post_id]);
$comments = $comments_stmt->fetchAll();
?>

<div class="post-details">
    <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image" class="post-image">
    <div class="post-comments">
        <h2>Comments</h2>
        <?php foreach ($comments as $comment): ?>
            <div class="comment">
                <strong><?php echo htmlspecialchars($comment['username']); ?>:</strong>
                <p><?php echo htmlspecialchars($comment['content']); ?></p>
            </div>
        <?php endforeach; ?>

        <!-- Comment Form -->
        <form method="POST" action="submit_comment.php">
            <textarea name="content" placeholder="Write a comment..." required></textarea>
            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
            <button type="submit">Post Comment</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>