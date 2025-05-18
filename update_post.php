<?php
include 'includes/db.php';

// Get data from form
$post_id = $_POST['post_id'];
$title = $_POST['title'];
$description = $_POST['description'];
$gender = $_POST['gender'];
$top = $_POST['top'];
$topColor = $_POST['top_color'];
$pants = $_POST['pants'];
$pantsColor = $_POST['pants_color'];
$shoes = $_POST['shoes'];
$shoesColor = $_POST['shoes_color'];
$references = $_POST['references'];
$referencesColor = $_POST['references_color'];

// Fetch current image list
$stmt = $pdo->prepare("SELECT images FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
$current = $stmt->fetch();
$currentImages = !empty($current['images']) ? explode(',', $current['images']) : [];

// Remove selected images
$imagesToRemove = $_POST['remove_images'] ?? [];
$remainingImages = [];

foreach ($currentImages as $img) {
    if (!in_array($img, $imagesToRemove)) {
        $remainingImages[] = $img;
    } else {
        $imgPath = "uploads/" . $img;
        if (file_exists($imgPath)) {
            unlink($imgPath);
        }
    }
}

// Upload new images
$newImages = [];
if (!empty($_FILES['new_images']['name'][0])) {
    foreach ($_FILES['new_images']['tmp_name'] as $key => $tmpName) {
        $originalName = $_FILES['new_images']['name'][$key];
        $sanitizedOriginalName = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $originalName);
        $newFilename = uniqid('post_', true) . "_" . $sanitizedOriginalName;
        $destination = "uploads/" . $newFilename;

        if (move_uploaded_file($tmpName, $destination)) {
            $newImages[] = $newFilename;
        }
    }
}

// Final image list
$finalImages = array_merge($remainingImages, $newImages);
$imagesString = implode(',', $finalImages);

// Update post
$updateStmt = $pdo->prepare("UPDATE posts SET title = ?, description = ?, gender = ?, top = ?, top_color = ?, pants = ?, pants_color = ?, shoes = ?, shoes_color = ?, `references` = ?, references_color = ?, images = ? WHERE id = ?");
$updateStmt->execute([
    $title,
    $description,
    $gender,
    $top,
    $topColor,
    $pants,
    $pantsColor,
    $shoes,
    $shoesColor,
    $references,
    $referencesColor,
    $imagesString,
    $post_id
]);

header("Location: moreinfo.php?id=" . $post_id);
exit;
