<?php include 'includes/header.php'; ?>
<?php require 'includes/db.php'; ?>

<div class="page-title">
  <h2>Explore Creations</h2>
</div>

<div class="grid-container">
  <?php foreach ($posts as $post): ?>
    <div class="post-card">
      <a href="view.php?id=<?= $post['id']; ?>">
        <img src="uploads/<?= htmlspecialchars($post['image']); ?>" alt="Outfit">
      </a>
    </div>
  <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>
