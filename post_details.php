<?php
require 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_POST['gender'], $_POST['top'], $_POST['pants'], $_POST['shoes'])) {
    echo "<p>Missing data. Please go back and select a character and clothing items.</p>";
    include 'includes/footer.php';
    exit;
}

$gender = $_POST['gender'];
$top = $_POST['top'];
$pants = $_POST['pants'];
$shoes = $_POST['shoes'];
?>

<div class="post-details-container">
  <h2>Finish Your Creation</h2>

  <form action="submit_post.php" method="POST">
    <div class="preview-box">
      <img src="assets/<?= $gender === 'f' ? 'female_base.png' : 'male_base.png' ?>" alt="Base Character">
      <img src="assets/clothes/<?= $top . $gender ?>.png" alt="Top">
      <img src="assets/clothes/<?= $pants . $gender ?>.png" alt="Pants">
      <img src="assets/clothes/<?= $shoes . $gender ?>.png" alt="Shoes">
    </div>

    <label for="title">Title:</label>
    <input type="text" name="title" id="title" required>

    <label for="description">Description:</label>
    <textarea name="description" id="description" rows="5" placeholder="Describe your outfit..."></textarea>

    <input type="hidden" name="gender" value="<?= htmlspecialchars($gender) ?>">
    <input type="hidden" name="top" value="<?= htmlspecialchars($top) ?>">
    <input type="hidden" name="pants" value="<?= htmlspecialchars($pants) ?>">
    <input type="hidden" name="shoes" value="<?= htmlspecialchars($shoes) ?>">

    <button type="submit" class="btn">Post Outfit</button>
  </form>
</div>

<?php include 'includes/footer.php'; ?>
