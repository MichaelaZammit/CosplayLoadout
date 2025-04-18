<?php
require 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

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

<div class="edit-profile-container">
  <h2>Edit Profile</h2>

  <form method="POST" enctype="multipart/form-data">
    <label for="profile_image">Profile Image:</label><br>
    <input type="file" name="profile_image" accept="image/*"><br><br>

    <label for="bio">Bio:</label><br>
    <textarea name="bio" rows="5" cols="50"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea><br><br>

    <button type="submit" class="btn">Save Changes</button>
  </form>
</div>

<?php include 'includes/footer.php'; ?>
