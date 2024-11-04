<?php
session_start();

// ตรวจสอบว่าเข้าสู่ระบบหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <title>ติดต่อเรา</title>
    <style>
/* Global Styles */
body {
    font-family: 'Itim', cursive; /* ใช้ฟอนต์ Itim */
    background-color: #f4f4f4;
    background: linear-gradient(to right, #e0f2f1 50%, #ffffff 50%);
    margin: 0;
    padding: 0;
    text-align: center;
}

        .form-container {
            margin: 20px auto;
            width: 80%;
            max-width: 500px;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .styled-button {
            margin-top: 10px;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #00796b;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .styled-button:hover {
            background-color: #004d40;
        }
        .map-container {
            margin: 20px auto;
            width: 80%;
            max-width: 600px;
            height: 400px;
            border: 1px solid #ccc;
            border-radius: 5px;
            overflow: hidden;
        }
        .contact-info {
            margin: 20px auto;
            text-align: left;
            width: 80%;
            max-width: 500px;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h2>เมนูหลัก</h2>
        <ul>
        <li><a href="admin.php">หน้าแรก</a></li>
            <li><a href="article.php">สถานที่ท่องเที่ยว</a></li>
            <li><a href="layout.php">ข่าวสาร</a></li>
            <li><a href="checkpoints.php">จุดเช็คอิน</a></li>
            <li><a href="about.php">เกี่ยวกับเรา</a></li>
            <li><a href="contact.php">ติดต่อเรา</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
        </ul>
        <a href="logout.php"><button class="btn logout">Logout</button></a>
    </div>
    <!-- ปุ่มเปิด-ปิด Sidebar -->
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
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
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }

        .logout-btn:hover {
            background-color: #d32f2f;
        }
</style>

        <script>
    function toggleSidebar() {
        document.getElementById("sidebar").classList.toggle("active");
        document.querySelector(".content").classList.toggle("shifted");
    }
</script>

<div class="form-container">
    <h2>ติดต่อเรา</h2>
    <form action="send_contact.php" method="post">
        <input type="text" name="name" placeholder="ชื่อ" required><br><br>
        <input type="email" name="email" placeholder="อีเมล" required><br><br>
        <textarea name="message" placeholder="ข้อความ" required></textarea><br><br>
        <button type="submit" class="styled-button">ส่งข้อความ</button>
    </form>
</div>

<div class="contact-info">
    <h3>ข้อมูลติดต่อ</h3>
    <p><strong>อีเมล:</strong> dee055909@gmail.com</p>
    <p><strong>เบอร์โทร:</strong> +66 80 147 5044</p>
    <p><strong>ที่อยู่:</strong> ประจวบคีรีขันธ์ 77110 อำเภอหัวหิน ตำบล หนองแก</p>
</div>

<div class="map-container">
    <h3>ที่อยู่ของเรา</h3>
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d62323.22815515674!2d99.91703137166367!3d12.502782069260228!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30fdac42d71b5507%3A0x40223bc2c381270!2z4LiV4Liz4Lia4LilIOC4q-C4meC4reC4h-C5geC4gSDguK3guLPguYDguKDguK3guKvguLHguKfguKvguLTguJkg4Lib4Lij4Liw4LiI4Lin4Lia4LiE4Li14Lij4Li14LiC4Lix4LiZ4LiY4LmMIDc3MTEw!5e0!3m2!1sth!2sth!4v1730579372521!5m2!1sth!2sth" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</div>

</body>
</html>
