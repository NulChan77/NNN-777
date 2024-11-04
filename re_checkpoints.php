<?php
session_start();

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "", "user_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ดึงข้อมูลจุดเช็คอินทั้งหมดจากฐานข้อมูล
$sql = "SELECT title, description, image_path FROM checkpoints";
$checkpoints_result = $conn->query($sql);

if (!$checkpoints_result) {
    echo "Error: " . $conn->error;
    exit();
}

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
/* Global Styles */
body {
    font-family: 'Itim', cursive; /* ใช้ฟอนต์ Itim */
    background-color: #f4f4f4;
    background: #ffffff;
    margin: 0;
    padding: 0;
    text-align: center;
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
        <!-- ปุ่มเข้าสู่ระบบและสมัครสมาชิก -->
        <button class="login-btn" onclick="window.location.href='login.php'">เข้าสู่ระบบ</button>
        <button class="signup-btn" onclick="window.location.href='register.php'">สมัครสมาชิก</button>
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

.login-btn:hover {
    background-color: #007bff; /* เปลี่ยนพื้นหลังเป็นน้ำเงินเมื่อ hover */
    color: white; /* รักษาสีตัวอักษรไว้ */
}

.signup-btn {
    background-color: black; /* พื้นหลังสีดำ */
    color: white; /* ตัวอักษรสีขาว */
    border: 2px solid yellow; /* ขอบสีเหลือง */
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease, border-color 0.3s ease;
    margin-bottom: 10px; /* เพิ่มระยะห่างทางด้านล่าง */
}

.signup-btn:hover {
    background-color: yellow; /* เปลี่ยนพื้นหลังเป็นเหลืองเมื่อ hover */
    color: black; /* เปลี่ยนสีตัวอักษรเป็นดำเมื่อ hover */
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
        </div>
    <?php endwhile; ?>
</div>
<p> </p>
<div class="container my-4 text-center">
    <a href="user_dashboard.php" class="btn btn-secondary">Back</a>
</div>

</body>
</html>
