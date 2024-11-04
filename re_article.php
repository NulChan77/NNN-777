<?php
session_start();
include 'db_connect.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

// ตรวจสอบว่ามีการส่ง id มาจาก URL หรือไม่
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    
    // ดึงข้อมูลบทความจากฐานข้อมูล
    $sql = "SELECT * FROM articles WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $article = $stmt->fetch();

    // จัดการการส่งคอมเมนต์
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['submit_comment']) && $user_id) {
            // เพิ่มคอมเมนต์ใหม่
            $comment = $_POST['comment'];
            $rating = $_POST['rating'];
            $image = '';

            // จัดการการอัปโหลดรูปภาพ
            if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                $image = 'uploads/' . basename($_FILES['image']['name']);
                move_uploaded_file($_FILES['image']['tmp_name'], $image);
            }

            // เพิ่มคอมเมนต์ลงในฐานข้อมูล
            $stmt = $pdo->prepare("INSERT INTO comments (article_id, user_id, comment, rating, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$id, $user_id, $comment, $rating, $image]);
        } elseif (isset($_POST['delete_comment']) && $user_id) {
            // ลบคอมเมนต์
            $comment_id = $_POST['comment_id'];
            $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
            $stmt->execute([$comment_id, $user_id]);
        } else {
            echo "<script>alert('คุณต้องเข้าสู่ระบบเพื่อแสดงความคิดเห็น');</script>";
        }
    }

    // ดึงข้อมูลคอมเมนต์สำหรับบทความ
    $comments = $pdo->prepare("SELECT * FROM comments WHERE article_id = :id ORDER BY created_at DESC");
    $comments->execute(['id' => $id]);
    $comments = $comments->fetchAll();
} else {
    $article = null;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรโมทสถานที่ท่องเที่ยว</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("active");
            document.querySelector(".content").classList.toggle("shifted");
        }
    </script>
        <style>
        body {
            font-family: 'Itim', sans-serif; /* ใช้ฟอนต์ Itim */
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2, h3 {
            color: #00796b;
        }
        .team-member {
            display: inline-block;
            width: 30%;
            margin: 10px;
            text-align: center;
        }
        img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
        }
        .contact-info {
            margin-top: 20px;
        }
        .login-btn {
    background-color: black; /* พื้นหลังสีดำ */
    color: white; /* ตัวอักษรสีขาว */
    border: 2px solid #007bff; /* ขอบสีน้ำเงิน */
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease, border-color 0.3s ease;
    margin-right: 10px; /* ระยะห่างทางด้านขวา */
    margin-bottom: 10px; /* ระยะห่างทางด้านล่าง */
}

.logout-btn {
    background-color: black; /* พื้นหลังสีดำ */
    color: white; /* ตัวอักษรสีขาว */
    border: 2px solid red; /* ขอบสีแดง */
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.logout-btn:hover {
    background-color: red; /* เปลี่ยนพื้นหลังเป็นแดงเมื่อ hover */
    color: white; /* รักษาสีตัวอักษรไว้ */
}
        /* Sidebar CSS */
        .sidebar {
            position: fixed;
            left: -250px; /* ซ่อน Sidebar ที่ตำแหน่งเริ่มต้น */
            top: 0;
            height: 100%;
            width: 250px;
            background-color: #222222;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            transition: left 0.3s ease;
            z-index: 100;
        }

        .sidebar.active {
            left: 0; /* แสดง Sidebar เมื่อมี class active */
        }

        .sidebar h2 {
            color: white;
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin-bottom: 20px;
        }

        .sidebar ul li a {
    color: white; /* ตัวอักษรสีขาว */
    text-decoration: none; /* ไม่มีขีดเส้นใต้ */
    font-size: 18px;
    display: block; /* ให้ลิงก์แสดงเป็นบล็อก */
    padding: 10px; /* เว้นพื้นที่ภายใน */
    background-color: #000000; /* พื้นหลังสีดำ */
    border-radius: 5px; /* มุมโค้ง */
    text-align: center; /* จัดข้อความกลาง */
    transition: background-color 0.3s, border 0.3s; /* เอฟเฟกต์การเปลี่ยนสี */
    border: 2px solid transparent; /* ขอบโปร่งใสเพื่อให้มีการเปลี่ยนแปลง */
}

.sidebar ul li a:hover {
    background-color: #222222; /* เปลี่ยนพื้นหลังเมื่อ hover */
    border: 2px solid white; /* ขอบสีขาวเมื่อ hover */
}


        .content {
            padding: 20px;
            transition: margin-left 0.3s ease;
            margin-left: 0; /* ระยะห่างจากด้านซ้าย */
        }

        .content.shifted {
            margin-left: 250px; /* ขยับเนื้อหาเมื่อ Sidebar ถูกเปิด */
        }

        .toggle-btn {
            position: fixed;
            left: 10px;
            top: 10px;
            background-color: #222222;
            color: white;
            padding: 8px;
            cursor: pointer;
            border: none;
            z-index: 101;
        }
        .container {
    margin-top: 20px;
}

.card-title {
    font-family: 'Itim', cursive;
    color: #333;
    text-align: center;
}

.carousel-image {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
    border-radius: 10px;
}

.card-text {
    margin-top: 20px;
    line-height: 1.6;
}

.comment {
    border-bottom: 1px solid #ddd;
    padding: 10px 0;
}

.comment p {
    margin-bottom: 5px;
}

.comment img {
    width: 100px;
    height: auto;
    border-radius: 5px;
}

.mt-4 {
    margin-top: 20px;
}

.btn-primary, .btn-info {
    background-color: #007bff;
    border-color: #007bff;
    color: #fff;
}

.carousel-inner ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.carousel-inner li {
    padding: 5px 0;
}

.carousel-inner li a {
    text-decoration: none;
    color: #007bff;
}

.carousel-inner li a:hover {
    text-decoration: underline;
    color: #0056b3;
}

.alert-warning {
    background-color: #fff3cd;
    color: #856404;
    padding: 10px;
    border-radius: 5px;
}

.content-container {
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 10px;
    box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
}
.btn-info {
    background-color: #28a745; /* Green background for a travel-related theme */
    color: #fff; /* White text for better contrast */
    border: none; /* Remove default border */
    font-weight: bold;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 16px;
    transition: background-color 0.3s ease, box-shadow 0.3s ease; /* Smooth transition */
}

.btn-info:hover {
    background-color: #218838; /* Darker green on hover */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow effect on hover */
}
.container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}
.comments-section {
    margin-top: 30px;
}
.recent-articles {
    position: sticky;
    top: 20px;
}
.recent-articles .carousel-inner ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}
.recent-articles .carousel-inner li {
    padding: 5px 0;
}
.recent-articles .carousel-inner li a {
    text-decoration: none;
    color: #007bff;
}
.recent-articles .carousel-inner li a:hover {
    text-decoration: underline;
    color: #0056b3;
}
.carousel-image {
    width: 100%; /* Make image responsive */
    max-height: 400px; /* Set a maximum height */
    object-fit: cover; /* Maintain aspect ratio and cover the area */
    border-radius: 10px; /* Optional: round the corners of the images */
}
.card img {
    width: 100%; /* กำหนดให้รูปภาพเต็มความกว้างของ container */
    height: auto; /* ให้รูปภาพปรับขนาดตามสัดส่วนที่เหมาะสม */
    max-height: 400px; /* ลดความสูงสูงสุดของรูปภาพ */
    object-fit: cover; /* ทำให้รูปภาพครอบคลุมพื้นที่ทั้งหมดโดยไม่บิดเบี้ยว */
    border-radius: 0; /* ทำให้มุมเป็นมุมตรง */
}
.carousel img {
    height: 500px; /* ปรับความสูงของภาพ */
    object-fit: cover; /* ให้รูปภาพเต็มพื้นที่โดยไม่บิดเบี้ยว */
}
/* Comment Section Styles */
.comment-section {
    margin-top: 20px; /* Adds space above the comments section */
}

.comment {
    border: 1px solid #ddd; /* Adds a light border around each comment */
    padding: 10px; /* Adds padding inside the comment box */
    margin-bottom: 15px; /* Space between comments */
}

.comment img {
    max-width: 100%; /* Makes sure images are responsive */
    height: auto; /* Maintains aspect ratio of images */
}

.btn-danger {
    margin-top: 10px; /* Adds margin to delete button */
}

    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h2>เมนูหลัก</h2>
        <ul>
        <li><a href="index.php">หน้าแรก</a></li>
        <li><a href="re_about.php">เกี่ยวกับเรา</a></li>
        <li><a href="re_checkpoints.php">จุดเช็คอิน</a></li>
        <li><a href="re_layout.php">ข่าวสาร</a></li>
        <li><a href="re_contact.php">ติดต่อเรา</a></li>
        </ul>
        <a href="logout.php"><button class="logout-btn">Logout</button></a>
    </div>

    <!-- Button to toggle Sidebar -->
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>

    <div class="container content-container">
        <div class="row">
            <div class="col-lg-8">
                <?php if ($article): ?>
                    <div class="card">
                        <div class="card-body">
                            <h1 class="card-title"><?= htmlspecialchars($article['title']) ?></h1>

                            <?php
                            // Convert JSON to an array for images
                            $images = json_decode($article['image'], true) ?? [];
                            if (!empty($images)): ?>
                                <div>
                                    <!-- Display large rectangular image -->
                                    <img src="uploads/<?= htmlspecialchars($images[0]) ?>" class="img-fluid" alt="Article Image" style="width: 100%; max-height: 600px; object-fit: cover;">
                                </div>
                            <?php else: ?>
                                <p class="alert alert-warning">No image available.</p>
                            <?php endif; ?>

                            <p class="card-text"><?= nl2br(htmlspecialchars($article['content'])) ?></p>

                            <!-- Display link to Google Maps -->
                            <h4>Location:</h4>
                            <?php if (!empty($article['map_link'])): ?>
                                <a href="<?= htmlspecialchars($article['map_link']) ?>" target="_blank" class="btn btn-info">View on Google Maps</a>
                            <?php else: ?>
                                <p class="alert alert-warning">No map link available.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                        <div class="mt-4">
                            <?php foreach ($comments as $comment): ?>
                                <div class="comment">
                                    <p><strong><?= htmlspecialchars($comment['comment']) ?></strong></p>
                                    <p>Rating: <?= htmlspecialchars($comment['rating']) ?> Stars</p>
                                    <?php if ($comment['image']): ?>
                                        <img src="<?= htmlspecialchars($comment['image']) ?>" alt="Comment Image">
                                    <?php endif; ?>
                                    <small class="text-muted">Posted on <?= htmlspecialchars($comment['created_at']) ?></small>

                                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']): ?>
                                        <form method="POST" style="display: inline;">
                                            <button type="submit" name="delete_comment" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript for dropdown toggle -->
    <script>
        function toggleDropdown() {
            var dropdown = document.getElementById("dropdownMenu");
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        }

        // ปิดเมนูเมื่อคลิกนอกเมนู
        window.onclick = function(event) {
            if (!event.target.matches('.profile, .profile *')) {
                document.getElementById("dropdownMenu").style.display = "none";
            }
        }
    </script>
</body>
</html>