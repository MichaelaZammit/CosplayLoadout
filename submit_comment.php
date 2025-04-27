<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = trim($_POST['content']);
    $post_id = intval($_POST['post_id']);
    $user_id = $_SESSION['user_id'];

    if (!empty($content)) {
        $stmt = $pdo->prepare("INSERT INTO comments (user_id, post_id, content, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$user_id, $post_id, $content]);
    }
}

header("Location: moreinfo.php?id=$post_id");
exit;
?>
