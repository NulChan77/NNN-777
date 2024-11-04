<?php
session_start();

// ตรวจสอบว่าเข้าสู่ระบบหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "", "user_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ดึงข้อมูลจุดเช็คอินทั้งหมดจากฐานข้อมูล
$sql = "SELECT id, title, description, image_path FROM checkpoints";
$checkpoints_result = $conn->query($sql);

if (!$checkpoints_result) {
    echo "Error: " . $conn->error;
    exit();
}

// ดึงข้อมูลผู้ใช้
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// จัดการการส่งคอมเมนต์
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit_comment'])) {
        $checkpoint_id = $_POST['checkpoint_id'];
        $comment = $_POST['comment'];

        // เพิ่มคอมเมนต์ลงในฐานข้อมูล
        $stmt = $conn->prepare("INSERT INTO comments (checkpoint_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $checkpoint_id, $user_id, $comment);
        $stmt->execute();
    } elseif (isset($_POST['delete_comment'])) {
        $comment_id = $_POST['comment_id'];

        // ลบคอมเมนต์จากฐานข้อมูล
        $stmt = $conn->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $comment_id, $user_id);
        $stmt->execute();
    }
}

// ดึงข้อมูลคอมเมนต์สำหรับแต่ละจุดเช็คอิน
$comments_sql = "SELECT c.id, c.comment, c.created_at, c.user_id, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE checkpoint_id = ?";
$comments_stmt = $conn->prepare($comments_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จุดเช็คอิน</title>
    
    <!-- Google Font: Itim -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Itim', sans-serif; /* ใช้ฟอนต์ Itim */
            background-color: #f4f4f4;
            color: #333;
            text-align: center;
            margin: 0;
            padding: 20px;
        }
        .checkpoint-list {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }
        .checkpoint-item {
            width: 70%; /* ความกว้างของแต่ละจุดเช็คอินที่ 70% ของหน้าจอ */
            max-width: 800px; /* กำหนดความกว้างสูงสุด */
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            text-align: left;
            margin: auto;
        }
        .checkpoint-item img {
            width: 100%;
            height: 300px; /* กำหนดความสูงของภาพ */
            object-fit: cover;
        }
        .checkpoint-content {
            padding: 20px;
        }
        .checkpoint-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin: 0;
        }
        .checkpoint-description {
            font-size: 16px;
            line-height: 1.6;
            color: #666;
            margin-top: 10px;
        }
        .comment-section {
            padding: 10px 20px;
        }
        .comment-section h4 {
            margin: 0;
            font-size: 20px;
        }
        .comment {
            border-top: 1px solid #ccc;
            padding: 10px 0;
        }
        .comment-form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h2>เมนูหลัก</h2>
        <ul>
        <li><a href="user_dashboard.php">หน้าแรก</a></li>
        <li><a href="user_about.php">เกี่ยวกับเรา</a></li>
        <li><a href="user_checkpoints.php">จุดเช็คอิน</a></li>
        <li><a href="user_layout.php">ข่าวสาร</a></li>
        <li><a href="user_contact.php">ติดต่อเรา</a></li>
            </ul>
            <a href="logout.php" onclick="return confirmLogout();"><button class="logout-btn">Logout</button></a>
    </div>
    <!-- ปุ่มเปิด-ปิด Sidebar -->
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    <script>
    function confirmLogout() {
        return confirm("คุณแน่ใจหรือไม่ว่าต้องการล็อกเอ้า?");
    }
</script>

<style>
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
.btn-secondary {
    background-color: black; /* สีพื้นหลังเป็นสีดำ */
    color: white; /* ตัวอักษรสีขาว */
    border: 2px solid transparent; /* ขอบเริ่มต้นเป็นสีโปร่งใส */
    padding: 10px 20px; /* เพิ่ม padding ตามต้องการ */
    border-radius: 5px; /* มุมมน */
    text-decoration: none; /* ไม่มีเส้นใต้ */
    transition: border-color 0.3s ease; /* การเปลี่ยนแปลงสีขอบแบบนุ่มนวล */
}

.btn-secondary:hover {
    border-color: green; /* เปลี่ยนสีขอบเป็นสีเขียวเมื่อ hover */
    background-color: black; /* รักษาสีพื้นหลังเป็นสีดำเมื่อ hover */
}

</style>

        <script>
    function toggleSidebar() {
        document.getElementById("sidebar").classList.toggle("active");
        document.querySelector(".content").classList.toggle("shifted");
    }
</script>
<h2>จุดเช็คอิน</h2>
    <div class="checkpoint-list">
        <?php while ($checkpoint = $checkpoints_result->fetch_assoc()): ?>
            <div class="checkpoint-item">
                <img src="<?php echo $checkpoint['image_path'] ? $checkpoint['image_path'] : 'uploads/default.jpg'; ?>" alt="Checkpoint Image">
                <div class="checkpoint-content">
                    <h3 class="checkpoint-title"><?php echo htmlspecialchars($checkpoint['title']); ?></h3>
                    <p class="checkpoint-description"><?php echo htmlspecialchars($checkpoint['description']); ?></p>
                </div>

                <div class="comment-section">
                    <h4>ความคิดเห็น</h4>
                    <?php
                    // ดึงคอมเมนต์สำหรับจุดเช็คอินนี้
                    $comments_stmt->bind_param("i", $checkpoint['id']);
                    $comments_stmt->execute();
                    $comments_result = $comments_stmt->get_result();
                    while ($comment = $comments_result->fetch_assoc()): ?>
                        <div class="comment">
                            <strong><?php echo htmlspecialchars($comment['username']); ?></strong>: <?php echo htmlspecialchars($comment['comment']); ?>
                            <br>
                            <small>เมื่อ: <?php echo date("d-m-Y H:i:s", strtotime($comment['created_at'])); ?></small>
                            <?php if ($comment['user_id'] == $user_id): // ตรวจสอบว่าผู้ใช้เป็นเจ้าของคอมเมนต์ ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                    <button type="submit" name="delete_comment" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบความคิดเห็นนี้?');">ลบ</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                    
                    <form class="comment-form" method="POST">
                        <input type="hidden" name="checkpoint_id" value="<?php echo $checkpoint['id']; ?>">
                        <textarea name="comment" rows="4" placeholder="แสดงความคิดเห็น..."></textarea>
                        <button type="submit" name="submit_comment">ส่งความคิดเห็น</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    <p></p>
    <div class="container my-4 text-center">
        <a href="user_dashboard.php" class="btn btn-secondary">Back</a>
    </div>
<!-- Profile Section with Dropdown -->
<div class="profile" onclick="toggleDropdown()">
    <div class="profile-icon">
        <?php echo strtoupper(substr($user['firstname'], 0, 1)); ?>
    </div>
    <div class="dropdown-menu" id="dropdownMenu">
        <!-- แสดงชื่อผู้ใช้ -->
        <div>Username: <?php echo htmlspecialchars($user['username']); ?></div>
        <a href="edit_profile.php">แก้ไขข้อมูลส่วนตัว</a>
        <a href="logout.php">ล็อกเอ้า</a>
    </div>
</div>
<style>
    .profile {
        position: absolute;
        top: 20px;
        right: 20px;
        cursor: pointer;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .profile-icon {
        width: 40px;
        height: 40px;
        background-color: #333;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 20px;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        top: 60px;
        right: 20px;
        background-color: #fff;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
        overflow: hidden;
        width: 200px; /* กำหนดความกว้างของเมนู */
    }

    .dropdown-menu a, .dropdown-menu div {
        display: block;
        padding: 10px 15px;
        color: #333;
        font-size: 14px;
        transition: background-color 0.3s ease;
        text-decoration: none;
    }

    .dropdown-menu div {
        font-weight: bold;
        color: #555;
    }

    .dropdown-menu a:hover {
        background-color: #f0f0f0;
    }

    .button-container .btn {
        padding: 10px 20px;
        margin: 5px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        display: inline-block;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-secondary {
        background-color: #28a745;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #218838;
    }

    .logout {
        background-color: #dc3545;
        color: white;
    }

    .logout:hover {
        background-color: #c82333;
    }
</style>
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
<?php
// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>