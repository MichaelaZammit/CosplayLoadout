<?php
ini_set('memory_limit', '512M');
session_start();
require 'includes/db.php';

$user_id = $_SESSION['user_id'];
$title = $_POST['title'];
$description = $_POST['description'];

$gender = $_POST['gender'];
$top = $_POST['top'] ?? '';
$pants = $_POST['pants'] ?? '';
$shoes = $_POST['shoes'] ?? '';
$references = $_POST['references'] ?? '';

$topColor = $_POST['top_color'] ?? '';
$pantsColor = $_POST['pants_color'] ?? '';
$shoesColor = $_POST['shoes_color'] ?? '';
$referencesColor = $_POST['references_color'] ?? '';

function imagePath($item, $gender, $color) {
    $base = "assets/clothes/";
    if (!$item) return null;
    $path1 = "{$base}{$item}{$gender}_{$color}.png";
    $path2 = "{$base}{$item}{$gender}.png";
    return file_exists($path1) ? $path1 : (file_exists($path2) ? $path2 : null);
}

// Base image
$basePath = "assets/" . ($gender === 'f' ? 'female_base.png' : 'male_base.png');
$base = imagecreatefrompng($basePath);
imagesavealpha($base, true);

$width = imagesx($base);
$height = imagesy($base);

$final = imagecreatetruecolor($width, $height);
imagesavealpha($final, true);
$transparent = imagecolorallocatealpha($final, 0, 0, 0, 127);
imagefill($final, 0, 0, $transparent);
imagecopy($final, $base, 0, 0, 0, 0, $width, $height);

// Layer merge
foreach ([imagePath($top, $gender, $topColor), imagePath($pants, $gender, $pantsColor), imagePath($shoes, $gender, $shoesColor), imagePath($references, $gender, $referencesColor)] as $layerPath) {
    if ($layerPath && file_exists($layerPath)) {
        $layer = imagecreatefrompng($layerPath);
        imagesavealpha($layer, true);
        imagecopy($final, $layer, 0, 0, 0, 0, $width, $height);
        imagedestroy($layer);
    }
}

$filename = uniqid("outfit_") . ".png";
imagepng($final, "uploads/" . $filename);
imagedestroy($final);

$stmt = $pdo->prepare("INSERT INTO posts (
    user_id, title, description, image, gender,
    top, top_color, pants, pants_color,
    shoes, shoes_color, reference_item, reference_color, created_at
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

$stmt->execute([
    $user_id, $title, $description, $filename, $gender,
    $top, $topColor, $pants, $pantsColor,
    $shoes, $shoesColor, $references, $referencesColor
]);

header("Location: profile.php");
exit;
