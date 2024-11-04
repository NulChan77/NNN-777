<?php
include 'db_connect.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่ามีการส่ง id มาจาก URL หรือไม่
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // ดึงข้อมูลบทความจากฐานข้อมูล
    $sql = "SELECT * FROM articles WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $article = $stmt->fetch();

    // ถ้ามีการลบบทความ
    if (isset($_GET['delete_article'])) {
        // ตรวจสอบว่าบทความถูกค้นพบ
        if ($article) {
            // แปลง JSON เป็นอาร์เรย์สำหรับรูปภาพ
            $images = json_decode($article['image'], true) ?? [];
            // ลบไฟล์ภาพทั้งหมดที่เกี่ยวข้องกับบทความ
            foreach ($images as $image) {
                unlink("uploads/" . $image); // ลบไฟล์จากเซิร์ฟเวอร์
            }

            // ลบบทความจากฐานข้อมูล
            $sql = "DELETE FROM articles WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $id]);

            // เปลี่ยนเส้นทางไปยังหน้าบทความหลังจากลบ
            header("Location: article.php");
            exit();
        }
    }
} else {
    $article = null; // กำหนดให้ $article เป็น null หากไม่มี ID
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wiki Site</title>
    <!-- Google Font: Itim -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 56px;
            font-family: 'Itim', sans-serif; /* ใช้ฟอนต์ Itim */
        }
        .content-container {
            padding: 20px;
        }
        #map {
            height: 400px; /* กำหนดความสูงของ Google Map */
            width: 100%;
        }
        .carousel-image {
            width: 100%; /* ปรับความกว้างให้เต็มพื้นที่ */
            height: 300px; /* กำหนดความสูงของภาพใน carousel */
            object-fit: cover; /* ปรับการแสดงผลให้เหมาะสม */
            margin: auto; /* ศูนย์กลางภาพ */
        }
        .navbar {
            background-color: #004d99; /* ปรับสีของ Navbar */
        }
        .btn-primary {
            background-color: #ff6600;
            border: none;
        }
        .card-title {
            font-size: 1.5rem;
            color: #333;
        }
        .back-button:hover {
            background-color: #218838; /* สีเขียวเข้มเมื่อ hover */
            transform: scale(1.05); /* ขยายเมื่อ hover */
        }
        .carousel-item {
            padding: 20px;
            background-color: #000; /* เปลี่ยนพื้นหลังเป็นสีดำ */
        }
        .carousel-item ul {
            padding: 0;
            margin: 0;
            list-style: none;
        }
        .carousel-item ul li {
            padding: 10px 0;
            font-size: 1.2rem; /* ขยายขนาดตัวหนังสือ */
            text-align: center;
        }
        .carousel-item a {
            text-decoration: none;
            color: #fff; /* เปลี่ยนสีข้อความเป็นสีขาว */
        }
        .carousel-item a:hover {
            color: #ffcc00; /* เปลี่ยนสีเมื่อ hover */
        }
        .col-lg-6 {
            max-width: 100%; /* ปรับขนาดคอลัมน์ให้เต็มพื้นที่ */
        }
        .card {
            padding: 20px;
        }
    </style>
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

    <div class="container content-container">
        <div class="row">
            <div class="col-lg-8">
                <?php if ($article): ?>
                    <div class="card">
                        <div class="card-body">
                            <h1 class="card-title"><?= htmlspecialchars($article['title']) ?></h1>

                            <?php
                            // แปลง JSON เป็นอาร์เรย์สำหรับรูปภาพ
                            $images = json_decode($article['image'], true) ?? [];
                            if ($images): ?>
                                <div id="imageCarousel" class="carousel slide" data-ride="carousel">
                                    <div class="carousel-inner">
                                        <?php foreach ($images as $index => $image): ?>
                                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                                <img src="uploads/<?= htmlspecialchars($image) ?>" class="carousel-image" alt="Article Image">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <a class="carousel-control-prev" href="#imageCarousel" role="button" data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#imageCarousel" role="button" data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </div>
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

                            <a href="edit_article.php?id=<?= $article['id'] ?>" class="btn btn-warning">Edit Article</a>

                            <!-- Delete Article Button -->
                            <form action="article.php?id=<?= $article['id'] ?>&delete_article=true" method="POST" style="display:inline;">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this article? This action cannot be undone.');">Delete Article</button>
                            </form>
                        </div>
                    </div>

                <?php else: ?>
                    <div class="alert alert-danger" role="alert">
                        Article not found or invalid ID.
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-4">
                <!-- Sidebar สำหรับบทความเพิ่มเติม -->
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Recent Articles</h4>

                        <!-- เริ่มต้น Carousel -->
                        <div id="recentArticlesCarousel" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner">
                                <?php
                                // ดึงข้อมูลบทความล่าสุดจากฐานข้อมูล
                                $recentArticles = $pdo->query("SELECT id, title FROM articles ORDER BY created_at DESC LIMIT 12")->fetchAll();
                                $chunks = array_chunk($recentArticles, 4); // แบ่งเป็นกลุ่มละ 4 บทความ

                                foreach ($chunks as $index => $chunk): ?>
                                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                        <ul class="list-unstyled">
                                            <?php foreach ($chunk as $recentArticle): ?>
                                                <li>
                                                    <a href="article.php?id=<?= $recentArticle['id'] ?>"><?= htmlspecialchars($recentArticle['title']) ?></a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- ปุ่มควบคุมการเลื่อน -->
                            <a class="carousel-control-prev" href="#recentArticlesCarousel" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#recentArticlesCarousel" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap & JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <div class="container my-4 text-center">
        <a href="admin.php" class="btn btn-secondary">Back</a>
    </div>

</body>
</html>
