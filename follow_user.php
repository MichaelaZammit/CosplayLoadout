<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$current_user_id = $_SESSION['user_id']; // who is following
$follow_user_id = intval($_POST['follow_user_id']); // who you want to follow

// Check if already following
$stmt = $pdo->prepare("SELECT * FROM followers WHERE follower_id = ? AND following_id = ?");
$stmt->execute([$current_user_id, $follow_user_id]);
$exists = $stmt->fetch();

if ($exists) {
    // Already following => unfollow
    $delete = $pdo->prepare("DELETE FROM followers WHERE follower_id = ? AND following_id = ?");
    $delete->execute([$current_user_id, $follow_user_id]);
} else {
    // Not following => follow
    $insert = $pdo->prepare("INSERT INTO followers (follower_id, following_id) VALUES (?, ?)");
    $insert->execute([$current_user_id, $follow_user_id]);
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
?>
