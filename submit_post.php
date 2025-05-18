<?php
include 'includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
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

    // ✅ Handle multiple image uploads
    $imageNames = [];

    if (!empty($_FILES['post_images']['name'][0])) {
        $uploadDir = 'uploads/';
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        foreach ($_FILES['post_images']['tmp_name'] as $index => $tmpName) {
            $originalName = $_FILES['post_images']['name'][$index];
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            if (in_array($extension, $allowed)) {
                $uniqueName = uniqid('post_', true) . '.' . $extension;
                $destination = $uploadDir . $uniqueName;

                if (move_uploaded_file($tmpName, $destination)) {
                    $imageNames[] = $uniqueName;
                }
            }
        }
    }

    $combinedImageNames = implode(',', $imageNames);

    // ✅ Insert the post into the database
    $stmt = $pdo->prepare("
    INSERT INTO posts 
    (user_id, title, description, gender, top, top_color, pants, pants_color, shoes, shoes_color, `references`, references_color, images) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
  
  $stmt->execute([
    $user_id, $title, $description,
    $gender, $top, $topColor,
    $pants, $pantsColor,
    $shoes, $shoesColor,
    $references, $referencesColor,
    $combinedImageNames
]);

  

    // Redirect to user's profile or feed
    header("Location: profile.php?id=" . $user_id);
    exit;
}
?>
