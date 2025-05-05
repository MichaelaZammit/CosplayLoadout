<?php
require 'includes/db.php';
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

    $topColor = $_POST['top_color'] ?? '';
    $pantsColor = $_POST['pants_color'] ?? '';
    $shoesColor = $_POST['shoes_color'] ?? '';

    // Build valid image paths
    $topImagePath = (!empty($top) && !empty($topColor)) ? "assets/clothes/{$top}{$gender}_{$topColor}.png" : null;
    $pantsImagePath = (!empty($pants) && !empty($pantsColor)) ? "assets/clothes/{$pants}{$gender}_{$pantsColor}.png" : null;
    $shoesImagePath = (!empty($shoes) && !empty($shoesColor)) ? "assets/clothes/{$shoes}{$gender}_{$shoesColor}.png" : null;

    // Generate output filename
    $image_name = uniqid("outfit_") . ".png";
    $output_path = 'uploads/' . $image_name;

    // Load base character image
    $basePath = 'assets/' . ($gender === 'f' ? 'female_base.png' : 'male_base.png');
    $base = imagecreatefrompng($basePath);
    imagealphablending($base, true);
    imagesavealpha($base, true);

    // Safely overlay a clothing image
    function overlayImage(&$base, $filePath) {
        if (!empty($filePath) && file_exists($filePath)) {
            $layer = imagecreatefrompng($filePath);
            imagecopy($base, $layer, 0, 0, 0, 0, imagesx($layer), imagesy($layer));
            imagedestroy($layer);
        }
    }

    // Apply clothing layers
    overlayImage($base, $topImagePath);
    overlayImage($base, $pantsImagePath);
    overlayImage($base, $shoesImagePath);

    // Save final image
    imagepng($base, $output_path);
    imagedestroy($base);

    // Save to database
    $stmt = $pdo->prepare("INSERT INTO posts (user_id, image, title, description) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $image_name, $title, $description]);

    header("Location: profile.php");
    exit;
} else {
    echo "Invalid request.";
}
?>
