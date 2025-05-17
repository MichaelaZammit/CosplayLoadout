<?php
include 'includes/header.php';

$gender = $_POST['gender'] ?? 'f';
$top = $_POST['top'] ?? null;
$pants = $_POST['pants'] ?? null;
$shoes = $_POST['shoes'] ?? null;
$references = $_POST['references'] ?? null;

$topColor = $_POST['top_color'] ?? '';
$pantsColor = $_POST['pants_color'] ?? '';
$shoesColor = $_POST['shoes_color'] ?? '';
$referencesColor = $_POST['references_color'] ?? '';

function imagePath($item, $gender, $color) {
    $base = "assets/clothes/";
    if (!$item) return null;

    $path1 = "{$base}{$item}{$gender}_{$color}.png";
    $path2 = "{$base}{$item}{$gender}.png";

    if ($color && file_exists($path1)) return $path1;
    if (file_exists($path2)) return $path2;

    return null;
}

$topImage = imagePath($top, $gender, $topColor);
$pantsImage = imagePath($pants, $gender, $pantsColor);
$shoesImage = imagePath($shoes, $gender, $shoesColor);
$referencesImage = imagePath($references, $gender, $referencesColor);
?>

<div class="create-page-wrapper">
  <div class="create-card">
    <div class="creator-layout">

      <div class="character-preview">
        <div class="preview-card">
          <div class="preview-box">
            <img src="assets/<?= $gender === 'f' ? 'female_base.png' : 'male_base.png' ?>" class="character-layer">

            <?php foreach ([$topImage, $pantsImage, $shoesImage, $referencesImage] as $img): ?>
              <?php if ($img && file_exists($img)): ?>
                <img src="<?= $img ?>" class="character-layer">
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="finish-form">
        <form action="submit_post.php" method="POST" class="creator-form">
          <input type="hidden" name="gender" value="<?= $gender ?>">
          <input type="hidden" name="top" value="<?= $top ?>">
          <input type="hidden" name="top_color" value="<?= $topColor ?>">
          <input type="hidden" name="pants" value="<?= $pants ?>">
          <input type="hidden" name="pants_color" value="<?= $pantsColor ?>">
          <input type="hidden" name="shoes" value="<?= $shoes ?>">
          <input type="hidden" name="shoes_color" value="<?= $shoesColor ?>">
          <input type="hidden" name="references" value="<?= $references ?>">
          <input type="hidden" name="references_color" value="<?= $referencesColor ?>">

          <div class="input-group">
            <label>Title</label>
            <input name="title" required>
          </div>

          <div class="input-group">
            <label>Description</label>
            <textarea name="description" required></textarea>
          </div>

          <button type="submit" class="submit-button">Submit</button>
        </form>
      </div>

    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
