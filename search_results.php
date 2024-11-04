<div class="card">
    <div class="card-body">
        <h1 class="card-title"><?= htmlspecialchars($article['title']) ?></h1>

        <?php
        // แปลง JSON เป็นอาร์เรย์สำหรับรูปภาพ
        $images = json_decode($article['image'], true) ?? [];
        if (!empty($images)): ?>
            <div>
                <img src="uploads/<?= htmlspecialchars($images[0]) ?>" class="carousel-image" alt="Article Image">
            </div>
        <?php else: ?>
            <p class="alert alert-warning">No image available.</p>
        <?php endif; ?>

        <p class="card-text"><?= nl2br(htmlspecialchars($article['content'])) ?></p>

        <!-- แสดงลิงก์ไปยัง Google Maps -->
        <h4>Location:</h4>
        <?php if (!empty($article['map_link'])): ?>
            <a href="<?= htmlspecialchars($article['map_link']) ?>" target="_blank" class="btn btn-info">
                View on Google Maps
            </a>
        <?php else: ?>
            <p class="alert alert-warning">No map link available.</p>
        <?php endif; ?>
    </div>
</div>
