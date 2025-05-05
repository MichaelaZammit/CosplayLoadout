<?php
session_start();
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && !empty($_POST['post_ids'])) {
    $user_id = $_SESSION['user_id'];
    $post_ids = $_POST['post_ids'];

    foreach ($post_ids as $post_id) {
        $post_id = intval($post_id);
        $stmt = $pdo->prepare("SELECT image FROM posts WHERE id = ? AND user_id = ?");
        $stmt->execute([$post_id, $user_id]);
        $post = $stmt->fetch();

        if ($post) {
            if (!empty($post['image']) && file_exists("uploads/" . $post['image'])) {
                unlink("uploads/" . $post['image']);
            }

            $delete = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
            $delete->execute([$post_id, $user_id]);
        }
    }
}

header("Location: profile.php");
exit;
