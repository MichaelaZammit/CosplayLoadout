<?php
include 'includes/header.php';
require 'includes/db.php';

// Fetch all outfits from the database (posts table)
$stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
$posts = $stmt->fetchAll();
?>

<h2>Explore Creations</h2>

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
