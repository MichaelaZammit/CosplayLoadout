<?php
include 'includes/header.php';

// Receiving clothes data
$gender = $_POST['gender'] ?? 'f';
$top = $_POST['top'] ?? null;
$pants = $_POST['pants'] ?? null;
$shoes = $_POST['shoes'] ?? null;
?>

<div class="create-page-wrapper">
  <div class="create-card">
    <div class="creator-layout">

      <!-- Character Preview -->
      <div class="character-preview">
        <div class="preview-card">
          <div class="preview-box">
            <img src="assets/<?= $gender === 'f' ? 'female_base.png' : 'male_base.png' ?>" alt="Base Character">
            <?php if ($top): ?>
              <img src="assets/clothes/<?= $top . $gender ?>.png" alt="Top">
            <?php endif; ?>
            <?php if ($pants): ?>
              <img src="assets/clothes/<?= $pants . $gender ?>.png" alt="Pants">
            <?php endif; ?>
            <?php if ($shoes): ?>
              <img src="assets/clothes/<?= $shoes . $gender ?>.png" alt="Shoes">
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Form for Title, Description and Submit -->
      <div class="finish-form">
        <form action="submit_post.php" method="POST" class="creator-form">

          <div class="input-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" placeholder="Name your outfit..." required>
          </div>

          <div class="input-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" placeholder="Describe your outfit..." required></textarea>
          </div>

          <!-- Hidden clothes info to submit -->
          <input type="hidden" name="gender" value="<?= $gender ?>">
          <input type="hidden" name="top" value="<?= $top ?>">
          <input type="hidden" name="pants" value="<?= $pants ?>">
          <input type="hidden" name="shoes" value="<?= $shoes ?>">

          <button type="submit" class="submit-button">Post Outfit</button>
        </form>
      </div>

    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
