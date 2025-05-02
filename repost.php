<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$current_user_id = $_SESSION['user_id'];
$post_id = intval($_POST['post_id']);

// Fetch original post
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
$original_post = $stmt->fetch();

if ($original_post) {
    $stmt = $pdo->prepare("
        INSERT INTO posts (user_id, image, title, description, repost_of, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $current_user_id,
        $original_post['image'],
        'Repost: ' . $original_post['title'],
        $original_post['description'],
        $post_id
    ]);
}

header('Location: profile.php?id=' . $current_user_id);
exit;
?>
