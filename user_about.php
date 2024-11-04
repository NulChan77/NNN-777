<?php
session_start();

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// เชื่อมต่อฐานข้อมูลเพื่อดึงข้อมูลผู้ใช้
$conn = new mysqli('localhost', 'root', '', 'user_management');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ดึงข้อมูลผู้ใช้
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เกี่ยวกับเรา</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">

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
    </style>
</head>
<body>
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

    </style>
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
            <a href="logout.php"><button class="logout-btn">Logout</button></a>
    </div>
    <!-- ปุ่มเปิด-ปิด Sidebar -->
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>

    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("active");
            document.querySelector(".content").classList.toggle("shifted");
        }
    </script>

<div class="container">
    <h1>เกี่ยวกับเรา</h1>
    <p>โครงการนี้จัดทำมาเพื่อส่งเสริมสถานที่ท่องเที่ยวและประเพณีต่างๆของปากน้ำปราณที่ไม่ค่อยมีคนรู้จักให้เป็นที่รู้จักกันมากขึ้น
และสามารถให้ผู้คนเข้าถึงข้อมูลผ่านเว็บไซต์ที่จัดทำเพื่อดึงดูดลูกค้าที่เข้ามารับชมให้เกิดความอยากไปเที่ยวในที่ที่ไม่รู้จัก
และสามารถแสดงความคึดเห็นต่างๆได้ผ่านเว็บไซต์เพื่อนำความคิดเห็นไปพัฒนาและปรับปรุงเว็บไซต์ต่อไปได้</p>

    <h2>ความเป็นมา</h2>
    <p>โครงการแหล่งท่องเที่ยว "ปากน้ำปราณ" มีแนวคิดพื้นฐานมาจากการเห็นศักยภาพที่ยังไม่ได้รับการพัฒนาอย่างเต็มที่ของพื้นที่ "ปากน้ำปราณ" ในจังหวัดประจวบคีรีขันธ์ ซึ่งมีความสวยงามและเป็นเอกลักษณ์เฉพาะตัว ทั้งในด้านธรรมชาติและวัฒนธรรมท้องถิ่น ในปัจจุบัน พื้นที่ปากน้ำปราณมีนักท่องเที่ยวเข้ามาเยือนเป็นจำนวนหนึ่ง แต่ขาดการวางแผนที่เป็นระบบเพื่อดึงดูดนักท่องเที่ยวให้มากขึ้น และเพิ่มการมีส่วนร่วมของชุมชนในการพัฒนาแหล่งท่องเที่ยว จึงเกิดแนวคิดในการจัดทำโครงการเพื่อพัฒนาพื้นที่ปากน้ำปราณให้เป็นแหล่งท่องเที่ยวที่ยั่งยืนและยกระดับคุณภาพชีวิตของคนในชุมชนไปพร้อมกัน</p>

    <h2>ข้อมูลการติดต่อ</h2>
    <div class="contact-info">
        <p>ที่อยู่: ประจวบคีรีขันธ์ 77110 อำเภอหัวหิน ตำบล หนองแก</p>
        <p>เบอร์โทรศัพท์: +66 801475044</p>
        <p>อีเมล: dee055909@gmail.com</p>
    </div>

    <h2>ติดตามเราได้ที่</h2>
    <p>
        <a href="https://www.facebook.com/yourcompany" target="_blank">Facebook</a> | 
        <a href="https://www.instagram.com/yourcompany" target="_blank">Instagram</a> | 
        <a href="https://www.linkedin.com/company/yourcompany" target="_blank">LinkedIn</a>
    </p>
</div>
</body>
</html>
