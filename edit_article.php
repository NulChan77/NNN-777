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

    // Get the existing map link
    $map_link = isset($article['map_link']) ? $article['map_link'] : ''; // ฟิลด์สำหรับลิงก์ Google Maps

    // แปลง JSON เป็นอาร์เรย์
    $images = json_decode($article['image'], true) ?? [];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // อัปเดตข้อมูลบทความ
        $title = $_POST['title'];
        $content = $_POST['content'];
        $map_link = $_POST['map_link'];   // ลิงก์ Google Maps

        // อัปโหลดรูปภาพหลายรูป
        $newImages = $_FILES['images']['name'];
        $uploadedImages = [];

        // อัปโหลดภาพใหม่
        for ($i = 0; $i < count($newImages); $i++) {
            if (!empty($newImages[$i])) {
                // ย้ายรูปภาพไปยังโฟลเดอร์ที่กำหนด
                $target_dir = "uploads/"; // แก้ไขที่เก็บรูปภาพตามต้องการ
                $target_file = $target_dir . basename($newImages[$i]);
                move_uploaded_file($_FILES['images']['tmp_name'][$i], $target_file);
                $uploadedImages[] = $newImages[$i]; // เพิ่มชื่อไฟล์ลงในอาร์เรย์
            }
        }

        // ถ้ามีการอัปโหลดภาพใหม่ ให้รวมกับภาพที่มีอยู่
        $allImages = array_merge($images, $uploadedImages);

        // แปลงอาร์เรย์รูปภาพเป็น JSON สำหรับบันทึกลงฐานข้อมูล
        $imageJson = json_encode($allImages);
        if (strlen($imageJson) > 65535) { // ขนาดสูงสุดของ VARCHAR (ถ้าใช้ VARCHAR)
            die("Error: The image data is too long to be stored in the database.");
        }

        // อัปเดตบทความในฐานข้อมูล
        $sql = "UPDATE articles SET title = :title, content = :content, image = :image, map_link = :map_link WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['title' => $title, 'content' => $content, 'image' => $imageJson, 'map_link' => $map_link, 'id' => $id]);

        // เปลี่ยนเส้นทางไปยังหน้าแสดงบทความหลังจากบันทึกการเปลี่ยนแปลง
        header("Location: article.php?id=" . $id);
        exit();
    }

    // ถ้ามีการลบภาพ
    if (isset($_GET['delete'])) {
        $imageToDelete = $_GET['delete'];
        if (in_array($imageToDelete, $images)) {
            // ลบไฟล์จากเซิร์ฟเวอร์
            if (unlink("uploads/" . $imageToDelete)) {
                // ลบชื่อภาพจากอาร์เรย์
                $images = array_diff($images, [$imageToDelete]);
                // อัปเดตฐานข้อมูล
                $imageJson = json_encode(array_values($images)); // รีเซ็ตอาร์เรย์เพื่อให้ไม่มีช่องว่าง
                $sql = "UPDATE articles SET image = :image WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['image' => $imageJson, 'id' => $id]);
                header("Location: edit_article.php?id=" . $id);
                exit();
            } else {
                echo "<p>Error deleting the image. Please try again.</p>";
            }
        } else {
            echo "<p>Image not found in the database.</p>";
        }
    }

    // ลบบทความ
    if (isset($_GET['delete_article'])) {
        // ลบไฟล์ภาพทั้งหมดที่เกี่ยวข้องกับบทความ
        foreach ($images as $image) {
            unlink("uploads/" . $image);
        }

        // ลบบทความจากฐานข้อมูล
        $sql = "DELETE FROM articles WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        // เปลี่ยนเส้นทางไปยังหน้าบทความหลังจากลบ
        header("Location: article.php");
        exit();
    }
} else {
    echo "<p>Invalid article ID.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Article</title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 56px;
        }
        .map-link {
            margin-top: 20px;
        }
        #map {
            height: 400px; /* กำหนดความสูงของ Google Map */
            width: 100%;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <a class="navbar-brand" href="#">WikiClone</a>
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

    <div class="container">
        <h1>Edit Article</h1>
        <?php if ($article): ?>
            <form action="edit_article.php?id=<?= $article['id'] ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($article['title']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea class="form-control" name="content" required><?= htmlspecialchars($article['content']) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="images">Upload Images (Optional)</label>
                    <input type="file" class="form-control-file" name="images[]" multiple>
                </div>
                
                <!-- แสดงภาพที่มีอยู่ -->
                <div class="form-group">
                    <label>Existing Images:</label><br>
                <style>
                    .form-control[name="content"] {
                     height: 300px; /* หรือขนาดตามที่คุณต้องการ */
                    }
                </style>
                    <?php if ($images): ?>
                        <?php foreach ($images as $image): ?>
                            <div class="existing-image">
                                <img src="uploads/<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($image) ?>" style="max-width: 200px; margin-bottom: 10px;">
                                <a href="edit_article.php?id=<?= $id ?>&delete=<?= htmlspecialchars($image) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this image?');">Delete</a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No images uploaded yet.</p>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="map_link">Google Maps Link</label>
                    <input type="text" class="form-control" name="map_link" value="<?= htmlspecialchars($map_link) ?>" placeholder="https://maps.google.com/?q=latitude,longitude" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Article</button>
            </form>
            
            <!-- Delete Article Button -->
            <form action="edit_article.php?id=<?= $article['id'] ?>&delete_article=true" method="POST" style="margin-top: 20px;">
                <button type="submit" class="btn btn-danger" onclick="return confirm('คุณแน่ใจว่าจะลบเนื้อหานี้?');">Delete Article</button>
            </form>

        <?php else: ?>
            <p>Article not found.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
