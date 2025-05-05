<?php include 'includes/header.php'; ?>

<div class="create-page-wrapper">
  <div class="create-card">
    <div class="creator-layout">

      <!-- Character Preview -->
      <div class="character-preview">
        <div class="preview-card">
          <div class="preview-box">
            <img id="base-layer" src="assets/male_base.png" class="character-layer" alt="Base Character">
            <img id="top-layer" class="character-layer" style="display:none;" alt="Top">
            <img id="pants-layer" class="character-layer" style="display:none;" alt="Pants">
            <img id="shoes-layer" class="character-layer" style="display:none;" alt="Shoes">
          </div>
        </div>
      </div>

      <!-- Clothing Form -->
      <div class="clothing-options">
        <form method="POST" class="creator-form" action="post_details.php" id="creatorForm">

          <!-- Gender -->
          <div class="form-section">
            <label class="section-label">Character</label>
            <div class="radio-group">
              <label><input type="radio" name="gender" value="f"> Female</label>
              <label><input type="radio" name="gender" value="m" checked> Male</label>
            </div>
          </div>

          <?php
          $clothingCategories = [
            'top' => ['top1', 'top2'],
            'pants' => ['pants1', 'pants2'],
            'shoes' => ['shoes1', 'shoes2']
          ];
          ?>

          <?php foreach ($clothingCategories as $type => $items): ?>
            <div class="form-section">
              <label class="section-label"><?= ucfirst($type) ?></label>
              <div class="option-grid">
                <?php foreach ($items as $item): ?>
                  <div class="image-option">
                    <label>
                      <input type="radio" name="<?= $type ?>" value="<?= $item ?>">
                      <img src="assets/clothes/<?= $item ?>m.png" class="option-image clickable" data-type="<?= $type ?>" data-item="<?= $item ?>" data-option-id="<?= $item ?>">
                    </label>
                    <div class="color-options hidden" id="colors-<?= $item ?>">
                      <?php
                      $colors = glob("assets/clothes/{$item}m_*.png");
                      foreach ($colors as $colorPath):
                        if (preg_match('/_([a-z]+)\.png$/', $colorPath, $m)):
                          $color = $m[1];
                      ?>
                        <label>
                          <input type="radio" name="color_<?= $item ?>" value="<?= $color ?>">
                          <img src="<?= $colorPath ?>" class="color-swatch" data-item="<?= $item ?>" data-color="<?= $color ?>" data-type="<?= $type ?>">
                        </label>
                      <?php endif; endforeach; ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endforeach; ?>

          <!-- Submit -->
          <div class="button-row">
            <button type="submit" class="submit-button">Post Outfit</button>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>

<!-- CSS to hide/show color boxes -->
<style>
  .color-options.hidden {
    display: none;
  }
</style>

<!-- JavaScript for preview and accordion -->
<script>
  const genderInputs = document.querySelectorAll('input[name="gender"]');
  const baseLayer = document.getElementById('base-layer');
  const topLayer = document.getElementById('top-layer');
  const pantsLayer = document.getElementById('pants-layer');
  const shoesLayer = document.getElementById('shoes-layer');

  let gender = 'm';
  let selection = {};

  // Update gender + base image
  genderInputs.forEach(input => {
    input.addEventListener('change', () => {
      gender = input.value;
      baseLayer.src = `assets/${gender === 'f' ? 'female_base' : 'male_base'}.png`;
      updateAllLayers();
    });
  });

  // Track all changes
  document.querySelectorAll('input[type="radio"]').forEach(input => {
    input.addEventListener('change', () => {
      selection[input.name] = input.value;
      updateAllLayers();
    });
  });

  // Preview logic
  function updateAllLayers() {
    const top = selection.top;
    const topColor = selection[`color_${top}`];
    if (top && topColor) {
      topLayer.src = `assets/clothes/${top}${gender}_${topColor}.png`;
      topLayer.style.display = 'block';
    } else {
      topLayer.style.display = 'none';
    }

    const pants = selection.pants;
    const pantsColor = selection[`color_${pants}`];
    if (pants && pantsColor) {
      pantsLayer.src = `assets/clothes/${pants}${gender}_${pantsColor}.png`;
      pantsLayer.style.display = 'block';
    } else {
      pantsLayer.style.display = 'none';
    }

    const shoes = selection.shoes;
    const shoesColor = selection[`color_${shoes}`];
    if (shoes && shoesColor) {
      shoesLayer.src = `assets/clothes/${shoes}${gender}_${shoesColor}.png`;
      shoesLayer.style.display = 'block';
    } else {
      shoesLayer.style.display = 'none';
    }
  }

  // Accordion behavior for color swatches
  document.querySelectorAll('.option-image.clickable').forEach(img => {
    img.addEventListener('click', () => {
      const clickedId = img.getAttribute('data-option-id');
      document.querySelectorAll('.color-options').forEach(opt => {
        opt.classList.add('hidden');
      });
      const showBox = document.getElementById(`colors-${clickedId}`);
      if (showBox) {
        showBox.classList.remove('hidden');
      }
    });
  });
</script>

<?php include 'includes/footer.php'; ?>
