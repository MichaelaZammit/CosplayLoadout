<?php
session_start();
require 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'includes/header.php'; // Now safe

$user_id = $_SESSION['user_id'];

// Fetch current user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bio = $_POST['bio'] ?? '';
    $profile_image = $user['profile_image'];

    // Handle image upload
    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "uploads/";
        $file_name = uniqid() . "_" . basename($_FILES["profile_image"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $profile_image = $file_name;
        }
    }

    // Update database
    $update = $pdo->prepare("UPDATE users SET bio = ?, profile_image = ? WHERE id = ?");
    $update->execute([$bio, $profile_image, $user_id]);

    header("Location: profile.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile - Cosplay Creator</title>
    <link rel="stylesheet" href="css/edit_profile.css">
</head>
<body>

<div class="edit-profile-wrapper">
  <div class="edit-profile-card">
    <h2>Edit Your Profile</h2>

    <form method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label for="profile_image">Profile Image:</label>
        <input type="file" name="profile_image" accept="image/*">
      </div>

      <div class="form-group">
        <label for="bio">Bio:</label>
        <textarea name="bio" rows="6" placeholder="Write something about yourself..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
      </div>

      <button type="submit" class="save-button">Save Changes</button>
    </form>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>
