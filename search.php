<?php
include 'db_connect.php'; // ไฟล์เชื่อมต่อฐานข้อมูล

$searchResults = []; // ตัวแปรเก็บผลการค้นหา

if (isset($_GET['q'])) {
    $search = $_GET['q'];
    $sql = "SELECT * FROM articles WHERE title LIKE :search OR content LIKE :search";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['search' => '%' . $search . '%']);
    
    while ($row = $stmt->fetch()) {
        $searchResults[] = $row; // เก็บผลการค้นหาไว้ในอาร์เรย์
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Articles</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <a class="navbar-brand" href="#">โปรโมทสถานที่ท่องเที่ยว</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="article.php">Articles</a></li>
                <li class="nav-item"><a class="nav-link" href="add_article.php">Add Article</a></li>
                <li class="nav-item"><a class="nav-link" href="search.php">Search</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="padding-top: 70px;">
        <h1 class="mt-5">Search Articles</h1>
        <form action="search.php" method="GET" class="my-4">
            <input type="text" name="q" placeholder="Search articles..." class="form-control" required>
            <input type="submit" value="Search" class="btn btn-primary mt-2">
            <a href="article.php" class="btn btn-secondary mt-3">Back to Articles</a>
        </form>

        <?php if (isset($_GET['q'])): ?>
            <h2>Results for "<?= htmlspecialchars($search) ?>"</h2>
            <?php if (count($searchResults) > 0): ?>
                <?php foreach ($searchResults as $row): ?>
                    <h2><a href='article.php?id=<?= $row['id'] ?>'><?= htmlspecialchars($row['title']) ?></a></h2>
                    <p><?= htmlspecialchars(substr($row['content'], 0, 100)) ?>...</p>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="alert alert-warning">No articles found matching your search.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
