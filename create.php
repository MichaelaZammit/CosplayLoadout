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
            <img id="references-layer" class="character-layer" style="display:none;" alt="References">
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

          <!-- Clothing Rows -->
          <div class="clothing-rows">
            <?php
            $clothingCategories = [
              'top' => ['top1', 'top2', 'top3', 'top4', 'top5'],
              'pants' => ['pants1', 'pants2', 'pants3', 'pants4', 'pants5'],
              'shoes' => ['shoes1', 'shoes2', 'shoes3', 'shoes4', 'shoes5'],
              'references' => ['img1', 'img2', 'img3', 'img4', 'img5']
            ];
            ?>

            <?php foreach ($clothingCategories as $type => $items): ?>
              <div class="clothing-row">
                <label class="section-label"><?= ucfirst($type) ?></label>
                <div class="option-row">
                  <?php foreach ($items as $item): ?>
                    <div class="image-option">
                      <label>
                        <input type="radio" name="<?= $type ?>" value="<?= $item ?>">
                        <img src="assets/clothes/<?= $item ?>m.png"
                          class="option-image clickable"
                          data-type="<?= $type ?>"
                          data-item="<?= $item ?>"
                          data-option-id="<?= $item ?>">
                      </label>
                    </div>
                  <?php endforeach; ?>
                  <!-- No clothing option -->
                  <div class="image-option">
                    <label>
                      <input type="radio" name="<?= $type ?>" value="" data-none="true">
                      <img src="assets/icons/no_<?= $type ?>.png"
                        class="option-image no-clothes-img"
                        alt="No <?= ucfirst($type) ?>">
                    </label>
                  </div>
                </div>

                <!-- Color options will be shown here -->
                <div class="color-options hidden" id="color-options-<?= $type ?>"></div>
              </div>
            <?php endforeach; ?>
          </div>

          <!-- Submit -->
          <div class="button-row">
            <button type="submit" class="submit-button">Post Outfit</button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

<script>
  const genderInputs = document.querySelectorAll('input[name="gender"]');
  const baseLayer = document.getElementById('base-layer');
  const topLayer = document.getElementById('top-layer');
  const pantsLayer = document.getElementById('pants-layer');
  const shoesLayer = document.getElementById('shoes-layer');
  const referencesLayer = document.getElementById('references-layer');

  let gender = 'm';
  let selection = {};

  genderInputs.forEach(input => {
    input.addEventListener('change', () => {
      gender = input.value;
      baseLayer.src = `assets/${gender === 'f' ? 'female_base' : 'male_base'}.png`;
      updateAllLayers();
    });
  });

  document.querySelectorAll('input[type="radio"]').forEach(input => {
    input.addEventListener('change', () => {
      if (input.dataset.none === "true") {
        selection[input.name] = null;
      } else {
        selection[input.name] = input.value;
      }
      updateAllLayers();
    });
  });

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

    // REFERENCES layer
    const references = selection.references;
    const referencesColor = selection[`color_${references}`];
    if (references && referencesColor) {
      referencesLayer.src = `assets/clothes/${references}${gender}_${referencesColor}.png`;
      referencesLayer.style.display = 'block';
    } else if (references) {
      referencesLayer.src = `assets/clothes/${references}${gender}.png`;
      referencesLayer.style.display = 'block';
    } else {
      referencesLayer.style.display = 'none';
    }
  }

  // Move swatches below the clothing row when clicked
  document.querySelectorAll('.option-image.clickable').forEach(img => {
    img.addEventListener('click', () => {
      const type = img.getAttribute('data-type');
      const item = img.getAttribute('data-item');

      // Hide all color swatch containers
      document.querySelectorAll('.color-options').forEach(opt => {
        opt.classList.add('hidden');
        opt.innerHTML = '';
      });

      // Grab swatches for selected item and insert them
      const source = document.getElementById(`colors-${item}`);
      const target = document.getElementById(`color-options-${type}`);
      if (source && target) {
        target.innerHTML = source.innerHTML;
        target.classList.remove('hidden');
      }
    });
  });

  // When a color swatch is clicked, store it and update preview
  document.addEventListener('change', function (e) {
    const input = e.target;
    if (input.name.startsWith('color_')) {
      const item = input.name.replace('color_', '');
      selection[`color_${item}`] = input.value;
      updateAllLayers();
    }
  });
</script>

<!-- Hidden color swatches (offscreen) -->
<?php foreach ($clothingCategories as $type => $items): ?>
  <?php foreach ($items as $item): ?>
    <div id="colors-<?= $item ?>" style="display: none;">
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
  <?php endforeach; ?>
<?php endforeach; ?>

<?php include 'includes/footer.php'; ?>
