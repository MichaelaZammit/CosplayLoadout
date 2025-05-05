<?php
include 'includes/header.php';

$gender = $_POST['gender'] ?? 'f';
$top = $_POST['top'] ?? null;
$pants = $_POST['pants'] ?? null;
$shoes = $_POST['shoes'] ?? null;

// Get color based on item name (like color_top2)
$topColor = $top ? ($_POST["color_{$top}"] ?? null) : null;
$pantsColor = $pants ? ($_POST["color_{$pants}"] ?? null) : null;
$shoesColor = $shoes ? ($_POST["color_{$shoes}"] ?? null) : null;

// Helper to build full file path
function imagePath($item, $gender, $color) {
  return ($item && $color) ? "assets/clothes/{$item}{$gender}_{$color}.png" : null;
}

$topImage = imagePath($top, $gender, $topColor);
$pantsImage = imagePath($pants, $gender, $pantsColor);
$shoesImage = imagePath($shoes, $gender, $shoesColor);
?>

<div class="create-page-wrapper">
  <div class="create-card">
    <div class="creator-layout">

      <!-- Character Preview -->
      <div class="character-preview">
        <div class="preview-card">
          <div class="preview-box">
            <img src="assets/<?= $gender === 'f' ? 'female_base.png' : 'male_base.png' ?>" class="character-layer" alt="Base Character">

            <?php if ($topImage && file_exists($topImage)): ?>
              <img src="<?= $topImage ?>" class="character-layer" alt="Top">
            <?php else: ?>
              <p style="color:red; font-size: 12px;">Top missing: <?= $topImage ?></p>
            <?php endif; ?>

            <?php if ($pantsImage && file_exists($pantsImage)): ?>
              <img src="<?= $pantsImage ?>" class="character-layer" alt="Pants">
            <?php else: ?>
              <p style="color:red; font-size: 12px;">Pants missing: <?= $pantsImage ?></p>
            <?php endif; ?>

            <?php if ($shoesImage && file_exists($shoesImage)): ?>
              <img src="<?= $shoesImage ?>" class="character-layer" alt="Shoes">
            <?php else: ?>
              <p style="color:red; font-size: 12px;">Shoes missing: <?= $shoesImage ?></p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Title & Description Form -->
      <div class="finish-form">
        <form action="submit_post.php" method="POST" class="creator-form">
          <div class="input-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required placeholder="Name your outfit...">
          </div>

          <div class="input-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" required placeholder="Describe your outfit..."></textarea>
          </div>

          <!-- Hidden Fields to pass to submit_post.php -->
          <input type="hidden" name="gender" value="<?= $gender ?>">
          <input type="hidden" name="top" value="<?= $top ?>">
          <input type="hidden" name="pants" value="<?= $pants ?>">
          <input type="hidden" name="shoes" value="<?= $shoes ?>">
          <input type="hidden" name="top_color" value="<?= $topColor ?>">
          <input type="hidden" name="pants_color" value="<?= $pantsColor ?>">
          <input type="hidden" name="shoes_color" value="<?= $shoesColor ?>">

          <button type="submit" class="submit-button">Post Outfit</button>
        </form>
      </div>

    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
