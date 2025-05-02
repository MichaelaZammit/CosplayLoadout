<?php
include 'includes/header.php';

// Set defaults
$gender = $_POST['gender'] ?? 'f';
$top = $_POST['top'] ?? null;
$pants = $_POST['pants'] ?? null;
$shoes = $_POST['shoes'] ?? null;
?>

<!-- Full Create Page -->
<div class="create-page-wrapper">
  <div class="create-card">

    <div class="creator-layout">
      
      <!-- Character Preview -->
      <div class="character-preview">
        <div class="preview-card">
          <div class="preview-box">
            <img src="assets/<?= $gender === 'f' ? 'female_base.png' : 'male_base.png' ?>" alt="Base Character" class="character-layer">
            <?php if ($top): ?>
              <img src="assets/clothes/<?= $top . $gender ?>.png" alt="Top" class="character-layer">
            <?php endif; ?>
            <?php if ($pants): ?>
              <img src="assets/clothes/<?= $pants . $gender ?>.png" alt="Pants" class="character-layer">
            <?php endif; ?>
            <?php if ($shoes): ?>
              <img src="assets/clothes/<?= $shoes . $gender ?>.png" alt="Shoes" class="character-layer">
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Clothing Selection Options -->
      <div class="clothing-options">
        <form action="create.php" method="POST" class="creator-form">

          <!-- Gender Selection -->
          <div class="form-section">
            <label class="section-label">Character</label>
            <div class="radio-group">
              <label><input type="radio" name="gender" value="f" <?= $gender === 'f' ? 'checked' : '' ?> onchange="this.form.submit();"> Female</label>
              <label><input type="radio" name="gender" value="m" <?= $gender === 'm' ? 'checked' : '' ?> onchange="this.form.submit();"> Male</label>
            </div>
          </div>

          <!-- Tops -->
          <div class="form-section">
            <label class="section-label">Tops</label>
            <div class="option-grid">
              <label class="image-option">
                <input type="radio" name="top" value="top1" <?= $top === 'top1' ? 'checked' : '' ?> onchange="this.form.submit();">
                <img src="assets/clothes/top1<?= $gender ?>.png" alt="Top 1" class="option-image">
              </label>
              <label class="image-option">
                <input type="radio" name="top" value="top2" <?= $top === 'top2' ? 'checked' : '' ?> onchange="this.form.submit();">
                <img src="assets/clothes/top2<?= $gender ?>.png" alt="Top 2" class="option-image">
              </label>
              <label class="image-option">
                <input type="radio" name="top" value="top3" <?= $top === 'top3' ? 'checked' : '' ?> onchange="this.form.submit();">
                <img src="assets/clothes/top3<?= $gender ?>.png" alt="Top 3" class="option-image">
              </label>
              <label class="image-option">
                <input type="radio" name="top" value="top4" <?= $top === 'top4' ? 'checked' : '' ?> onchange="this.form.submit();">
                <img src="assets/clothes/top4<?= $gender ?>.png" alt="Top 4" class="option-image">
              </label>
            </div>
          </div>

          <!-- Pants -->
          <div class="form-section">
            <label class="section-label">Pants</label>
            <div class="option-grid">
              <label class="image-option">
                <input type="radio" name="pants" value="pants1" <?= $pants === 'pants1' ? 'checked' : '' ?> onchange="this.form.submit();">
                <img src="assets/clothes/pants1<?= $gender ?>.png" alt="Pants 1" class="option-image">
              </label>
              <label class="image-option">
                <input type="radio" name="pants" value="pants2" <?= $pants === 'pants2' ? 'checked' : '' ?> onchange="this.form.submit();">
                <img src="assets/clothes/pants2<?= $gender ?>.png" alt="Pants 2" class="option-image">
              </label>
              <label class="image-option">
                <input type="radio" name="pants" value="pants3" <?= $pants === 'pants3' ? 'checked' : '' ?> onchange="this.form.submit();">
                <img src="assets/clothes/pants3<?= $gender ?>.png" alt="Pants 3" class="option-image">
              </label>
              <label class="image-option">
                <input type="radio" name="pants" value="pants4" <?= $pants === 'pants4' ? 'checked' : '' ?> onchange="this.form.submit();">
                <img src="assets/clothes/pants4<?= $gender ?>.png" alt="Pants 4" class="option-image">
              </label>
            </div>
          </div>

          <!-- Shoes -->
          <div class="form-section">
            <label class="section-label">Shoes</label>
            <div class="option-grid">
              <label class="image-option">
                <input type="radio" name="shoes" value="shoes1" <?= $shoes === 'shoes1' ? 'checked' : '' ?> onchange="this.form.submit();">
                <img src="assets/clothes/shoes1<?= $gender ?>.png" alt="Shoes 1" class="option-image">
              </label>
              <label class="image-option">
                <input type="radio" name="shoes" value="shoes2" <?= $shoes === 'shoes2' ? 'checked' : '' ?> onchange="this.form.submit();">
                <img src="assets/clothes/shoes2<?= $gender ?>.png" alt="Shoes 2" class="option-image">
              </label>
              <label class="image-option">
                <input type="radio" name="shoes" value="shoes3" <?= $shoes === 'shoes3' ? 'checked' : '' ?> onchange="this.form.submit();">
                <img src="assets/clothes/shoes3<?= $gender ?>.png" alt="Shoes 3" class="option-image">
              </label>
              <label class="image-option">
                <input type="radio" name="shoes" value="shoes4" <?= $shoes === 'shoes4' ? 'checked' : '' ?> onchange="this.form.submit();">
                <img src="assets/clothes/shoes4<?= $gender ?>.png" alt="Shoes 4" class="option-image">
              </label>
            </div>
          </div>

          <!-- Post Button -->
          <input type="hidden" name="confirmed" value="1">
          <div class="button-row">
            <button type="submit" formaction="post_details.php" class="submit-button">Post Outfit</button>
          </div>

        </form>
      </div>

    </div>

  </div>
</div>

<?php include 'includes/footer.php'; ?>
