<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$current_user_id = $_SESSION['user_id'];
$post_id = intval($_POST['post_id']);

// Check if already liked
$stmt = $pdo->prepare("SELECT * FROM likes WHERE user_id = ? AND post_id = ?");
$stmt->execute([$current_user_id, $post_id]);
$already_liked = $stmt->fetch();

if (!$already_liked) {
    // Insert new like
    $stmt = $pdo->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
    $stmt->execute([$current_user_id, $post_id]);
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
?>
