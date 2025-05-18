<?php
include 'includes/header.php';
include 'includes/db.php';

if (!isset($_GET['id'])) {
    echo "No post ID.";
    exit;
}

$post_id = intval($_GET['id']);

// Fetch the post from the DB
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    echo "Post not found.";
    exit;
}

$gender = $post['gender'];
$top = $post['top'];
$pants = $post['pants'];
$shoes = $post['shoes'];
$topColor = $post['top_color'] ?? '';
$pantsColor = $post['pants_color'] ?? '';
$shoesColor = $post['shoes_color'] ?? '';
$references = $post['references'] ?? null;
$referencesColor = $post['references_color'] ?? '';
$images = !empty($post['images']) ? explode(',', $post['images']) : [];

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
        <form action="update_post.php" method="POST" class="creator-form" enctype="multipart/form-data">
          <input type="hidden" name="post_id" value="<?= $post_id ?>">
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
            <input name="title" required value="<?= htmlspecialchars($post['title']) ?>">
          </div>

          <div class="input-group">
            <label>Description</label>
            <textarea name="description" required><?= htmlspecialchars($post['description']) ?></textarea>
          </div>

          <div class="input-group">
            <label>Current Images</label>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
              <?php foreach ($images as $img): ?>
                <div style="position:relative;">
                  <img src="uploads/<?= htmlspecialchars($img) ?>" style="width:80px; height:auto; border-radius:6px;">
                  <label style="display:block; text-align:center;">
                    <input type="checkbox" name="remove_images[]" value="<?= htmlspecialchars($img) ?>"> Remove
                  </label>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="input-group">
            <label>Add New Images</label>
            <input type="file" name="new_images[]" accept="image/*" multiple>
          </div>

          <button type="submit" class="submit-button">Update Post</button>
        </form>
      </div>

    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
