<?php
require 'includes/db.php';
ini_set('memory_limit', '512M');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $gender = $_POST['gender'] ?? 'f';
    $top = $_POST['top'] ?? '';
    $pants = $_POST['pants'] ?? '';
    $shoes = $_POST['shoes'] ?? '';

    // Generate image name
    $image_name = $top . $gender . '_' . $pants . $gender . '_' . $shoes . $gender . '.png';
    $output_path = 'uploads/' . $image_name;

    // Create base image
    $base = imagecreatefrompng('assets/' . ($gender === 'f' ? 'female_base.png' : 'male_base.png'));
    imagealphablending($base, true);
    imagesavealpha($base, true);

    // Overlay clothes
    foreach (['top', 'pants', 'shoes'] as $item) {
        $part = $_POST[$item] ?? '';
        if ($part) {
            $path = 'assets/clothes/' . $part . $gender . '.png';
            if (file_exists($path)) {
                $layer = imagecreatefrompng($path);
                $layer_resized = imagescale($layer, 300, 300);
                imagecopy($base, $layer_resized, 0, 0, 0, 0, imagesx($layer_resized), imagesy($layer_resized));
                imagedestroy($layer_resized);
                imagedestroy($layer);
            }
        }
    }
    

    // Save image
    imagepng($base, $output_path);
    imagedestroy($base);

    // Insert post into DB
    $stmt = $pdo->prepare("INSERT INTO posts (user_id, image, title, description) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $image_name, $title, $description]);

    header("Location: profile.php");
    exit;
} else {
    echo "Invalid request.";
}
?>
