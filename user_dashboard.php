<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // ถ้ายังไม่มีการล็อกอิน ให้กลับไปที่หน้า index.php
    header("Location: index.php");
    exit();
}
// สร้างการเชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "", "user_management");

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// สร้างคำสั่ง SQL เพื่อเลือกข้อมูลจากตาราง products
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

// เพิ่มคำสั่ง SQL เพื่อดึงข้อมูลแบนเนอร์จากตาราง settings หรือ banners
$banner_sql = "SELECT * FROM banners"; // สมมติว่าตารางที่เก็บแบนเนอร์ชื่อว่า banners
$banner_result = $conn->query($banner_sql);

// ดึงข้อความสำหรับ carousel
$sql_texts = "SELECT * FROM carousel_texts";
$texts_result = $conn->query($sql_texts);
$texts = [];
while ($row = $texts_result->fetch_assoc()) {
    $texts[$row['slide_number']] = $row['text_content'];
}

// อัปเดตข้อความ
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['slide_number'])) {
    $slide_number = intval($_POST['slide_number']);
    $new_text = $conn->real_escape_string($_POST['text_content']);

    // อัปเดตข้อความในฐานข้อมูล
    $update_sql = "UPDATE carousel_texts SET text_content = '$new_text' WHERE slide_number = $slide_number";
    if ($conn->query($update_sql) === TRUE) {
        // รีเฟรชหน้าเว็บหลังจากอัปเดต
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
    $sql = "INSERT INTO checkpoints (title, description, image_path) VALUES ('$title', '$description', '$image_path')";
    $conn->query($sql);
}

// ลบข้อมูลจุดเช็คอิน
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM checkpoints WHERE id = $id");
    header("Location: admin_checkpoints.php");
    exit();
}

// ดึงข้อมูลจุดเช็คอินทั้งหมด
$checkpoints_result = $conn->query("SELECT * FROM checkpoints ORDER BY created_at DESC");

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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <title>หน้าหลัก</title>
    <style>
/* Global Styles */
body {
    font-family: 'Itim', cursive; /* ใช้ฟอนต์ Itim */
    background: #FFF;
    margin: 0;
    padding: 0;
    text-align: center;
}


        #text-container {
            font-size: 36px;
            color: #2d5245;
            margin-bottom: 30px;
            min-height: 100px;
        }

        .styled-button {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 20px;
            background-color: white;
            color: #004d40;
            border: 2px solid #004d40;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }

        .styled-button:hover {
            background-color: #00796b;
            color: white;
        }

        .carousel-container {
            background-color: white;
            width: 80%;
            margin: 20px auto;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .carousel-slides {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }

        .carousel-slide {
            min-width: 100%;
            padding: 40px;
            box-sizing: border-box;
        }

        .carousel-buttons {
            position: absolute;
            top: 50%;
            width: 100%;
            display: flex;
            justify-content: space-between;
            transform: translateY(-50%);
        }

        .carousel-button {
            background-color: #004d40;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
        }

        .carousel-button:hover {
            background-color: #00796b;
        }

        footer {
            display: flex;
            justify-content: space-between;
            background: linear-gradient(to right, #004d40 50%, white 50%);
            padding: 20px;
            margin-top: 30px;
        }

        .footer-left {
            color: white;
            display: flex;
            gap: 15px;
        }

        .footer-left img {
            width: 30px;
            height: 30px;
            cursor: pointer;
        }

        .footer-right {
            text-align: left;
            padding-left: 20px;
        }

        .video-section {
            background-color: #00796b;
            padding: 30px;
            margin-top: 30px;
            color: white;
            text-align: center;
            z-index: 10; /* เพิ่ม z-index เพื่อให้อยู่ด้านหน้า */
            position: relative;
        }

        .video-section iframe {
            width: 80%;
            height: 400px;
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
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

        /* ปรับขนาดแบนเนอร์ให้ใหญ่ */
        .banner-container {
            width: 100%;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .banner-image {
            width: 100%;
            max-height: 500px; /* กำหนดความสูงสูงสุดของแบนเนอร์ */
            object-fit: cover; /* จัดการขนาดรูปให้เหมาะสมกับพื้นที่ */
        }

        /* สไตล์ปุ่มลบ */
        .delete-button {
            background-color: #f44336;
            color: white;
            padding: 8px;
            border: none;
            cursor: pointer;
            margin-top: 10px;
            font-size: 16px;
        }

        .delete-button:hover {
            background-color: #d32f2f;
        }
        textarea {
    width: 100%;
    height: 100px; /* ความสูงของ textarea */
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
    margin-top: 10px; /* ระยะห่างด้านบน */
    resize: none; /* ปิดการปรับขนาด textarea */
}

.styled-button {
    margin-top: 10px;
    padding: 10px 20px;
    font-size: 16px;
    background-color: #004d40;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.styled-button:hover {
    background-color: #00796b;
}
.checkpoints-section {
    background-color: #f0f0f0;
    padding: 20px;
    margin-top: 20px;
    text-align: center;
}

.checkpoints-section h2 {
    color: #004d40;
    font-size: 28px;
    margin-bottom: 20px;
}

.checkpoint-list {
    display: flex;
    justify-content: center;
    gap: 20px;
}

.checkpoint-item {
    background-color: white;
    padding: 15px;
    border-radius: 10px;
    width: 250px;
    text-align: center;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.checkpoint-item h3 {
    color: #004d40;
    font-size: 20px;
}

.checkpoint-item p {
    color: #333;
    margin-top: 10px;
    margin-bottom: 15px;
}
.form-container {
        margin: 20px auto;
        width: 90%;
        max-width: 500px;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        text-align: center;
    }
    
    h2 {
        color: #f0f0f0;
        margin-bottom: 20px;
    }
    
    .styled-button {
        padding: 10px 20px;
        font-size: 16px;
        background-color: #00796b;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin: 10px 5px;
    }

    .styled-button:hover {
        background-color: #004d40;
    }

    .checkpoint-list {
        margin: 20px auto;
        width: 90%;
        max-width: 800px;
    }

    .checkpoint-items-container {
        display: flex; /* ใช้ Flexbox เพื่อจัดเรียงเป็นแนวนอน */
        overflow-x: auto; /* ถ้ามีรายการมากจะสามารถเลื่อนแนวนอนได้ */
        padding: 10px 0; /* เพิ่มระยะห่างด้านบนและล่าง */
    }

    .checkpoint-item {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        margin: 10px;
        padding: 15px;
        flex: 0 0 auto; /* กำหนดให้แต่ละไอเท็มไม่ขยายเต็มความกว้าง */
        width: 200px; /* ปรับความกว้างของแต่ละไอเท็ม */
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
    }

    .checkpoint-item:hover {
        transform: scale(1.02);
    }

    img {
        max-width: 100%;
        height: auto;
        border-radius: 5px;
    }

    .checkpoint-title {
        color: #00796b;
        margin: 10px 0 5px;
    }

    p {
        color: #555;
    }
    .styled-button {
    padding: 10px 20px;
    font-size: 16px;
    background-color: #00796b; /* สีพื้นหลัง */
    color: white; /* สีข้อความ */
    border: none; /* ไม่มีกรอบ */
    border-radius: 5px; /* ขอบมน */
    cursor: pointer; /* เปลี่ยนเคอร์เซอร์เป็นมือเมื่อวางเหนือปุ่ม */
    transition: background-color 0.3s ease, transform 0.3s ease; /* เพิ่มเอฟเฟกต์การเปลี่ยนแปลง */
    margin: 10px 5px; /* เพิ่มระยะห่างระหว่างปุ่ม */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* เพิ่มเงา */
}

.styled-button:hover {
    background-color: #004d40; /* เปลี่ยนสีพื้นหลังเมื่อวางเมาส์ */
    transform: scale(1.05); /* ขยายขนาดปุ่มเมื่อวางเมาส์ */
}

.styled-button:active {
    transform: scale(0.95); /* หดขนาดปุ่มเมื่อคลิก */
}
.search-container {
    background-color: white; /* พื้นหลังสีขาว */
    padding: 20px;
    width: 100%; /* กว้างเต็มหน้าจอ */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* เงารอบกล่อง */
    text-align: center;
    margin-top: 20px; /* เพิ่มระยะห่างด้านบน */
    color: black; /* สีตัวอักษรหัวข้อเป็นสีดำ */
}

.search-box {
    display: inline-flex; /* ใช้ inline-flex เพื่อให้ขนาดเหมาะสม */
    align-items: center;
}

.search-box input[type="text"] {
    padding: 10px;
    width: 300px; /* กำหนดความกว้างที่ต้องการ */
    border: 2px solid #ccc;
    border-radius: 5px 0 0 5px;
    font-size: 16px;
}

.search-box button {
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 0 5px 5px 0;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.search-box button:hover {
    background-color: #0056b3; /* สีเข้มขึ้นเมื่อ hover */
}

.search-container h1, .search-container h2, .search-container h3 {
    color: black; /* สีของหัวข้อเป็นสีดำ */
}

    </style>
</head>
<body>

<div class="banner-container">
    <?php while ($banner = $banner_result->fetch_assoc()): ?>
        <div>
            <img src="uploads/<?php echo $banner['image_path']; ?>" class="banner-image">
            <form action="delete_banner.php" method="post" style="display:inline;">
                <input type="hidden" name="banner_id" value="<?php echo $banner['id']; ?>">
            </form>
        </div>
    <?php endwhile; ?>
</div>
<form action="upload_banner.php" method="post" enctype="multipart/form-data">


</form>
<div class="search-container">
    <h2>อยากจะเที่ยวที่ไหน</h2>
    <div class="search-box">
        <form action="user_search.php" method="GET">
            <button type="submit">ค้นหา</button>
        </form>
    </div>
</div>

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
        body {
    background-color: #e7f3e7; /* สีพื้นหลังของทั้งหน้า */
    margin: 0; /* ลบ margin เริ่มต้น */
    padding: 20px; /* เพิ่ม padding รอบๆ */
}

.checkpoint-list {
    display: flex;                /* ใช้ Flexbox */
    flex-direction: column;      /* จัดเรียงในแนวนอน */
    align-items: center;         /* จัดกลางในแนวนอน */
    padding: 20px;               /* เพิ่ม padding รอบๆ */
    background-color: #00796b;   /* สีพื้นหลัง */
    border-radius: 8px;          /* โค้งมุม */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* เงา */
    width: 100%;                 /* ให้เต็มความกว้าง */
    max-width: 1820px;           /* กำหนดความกว้างสูงสุด */
    margin: auto;                /* จัดกลางในหน้า */
}

.checkpoint-items {
    display: flex;                /* ใช้ Flexbox เพื่อจัดเรียง item ในแนวนอน */
    flex-wrap: wrap;             /* ทำให้ item สามารถห่อในกรณีที่มีขนาดใหญ่ */
    justify-content: center;      /* จัดกลาง item */
    width: 100%;                 /* ให้เต็มความกว้าง */
}

.checkpoint-item {
    margin: 10px;                /* ระยะห่างระหว่างจุดเช็คอิน */
    text-align: center;          /* จัดข้อความให้อยู่กลาง */
    flex: 1 1 200px;             /* ให้มีความกว้างเริ่มต้น 200px แต่ยืดได้ */
    max-width: 200px;            /* จำกัดความกว้างสูงสุด */
}

.checkpoint-item img {
    width: 100%;                 /* ให้รูปภาพขยายเต็มความกว้างของ item */
    height: 150px;               /* กำหนดความสูงคงที่ */
    object-fit: cover;           /* ทำให้รูปภาพไม่บิดเบี้ยว */
    border-radius: 5px;          /* โค้งมุมรูป */
}

.checkpoint-title {
    font-size: 16px;             /* ขนาดฟอนต์สำหรับ title */
    margin: 10px 0 5px;         /* ระยะห่างระหว่าง title และ description */
    overflow: hidden;            /* ซ่อนข้อความที่เกิน */
    text-overflow: ellipsis;     /* แสดง "..." สำหรับข้อความที่ยาวเกินไป */
    white-space: nowrap;         /* ไม่ให้ข้อความตัดบรรทัด */
}

.checkpoint-description {
    font-size: 14px;             /* ขนาดฟอนต์สำหรับ description */
    height: 40px;                /* กำหนดความสูงคงที่ */
    overflow: hidden;            /* ซ่อนข้อความที่เกิน */
    text-overflow: ellipsis;     /* แสดง "..." สำหรับข้อความที่ยาวเกินไป */
    white-space: nowrap;         /* ไม่ให้ข้อความตัดบรรทัด */
}

.styled-button {
    margin-top: 20px;            /* เพิ่มระยะห่างด้านบนของปุ่ม */
    padding: 10px 20px;          /* ขนาดปุ่ม */
    background-color: #28a745;   /* สีพื้นหลังของปุ่ม */
    color: white;                 /* สีข้อความของปุ่ม */
    border: none;                 /* ไม่มีกรอบ */
    border-radius: 5px;          /* โค้งมุมปุ่ม */
    cursor: pointer;              /* แสดงว่าเป็นปุ่มคลิกได้ */
}

.styled-button:hover {
    background-color: #218838;   /* สีพื้นหลังเมื่อ hover */
}
.carousel-container {
    position: relative; /* ใช้สำหรับการจัดตำแหน่งปุ่ม */
    overflow: hidden; /* ซ่อนสไลด์ที่อยู่นอกกรอบ */
}

.carousel-slides {
    display: flex; /* ใช้ Flexbox เพื่อจัดเรียงสไลด์ในแนวนอน */
    transition: transform 0.5s ease; /* เพิ่มการเปลี่ยนแปลงในการเลื่อนสไลด์ */
}

.carousel-slide {
    min-width: 100%; /* ทำให้แต่ละสไลด์มีความกว้างเต็มที่ */
    box-sizing: border-box; /* คำนวณ padding และ border ในความกว้าง */
    border: 2px solid #004d40; /* กำหนดสีและขนาดของกรอบ */
    border-radius: 8px; /* โค้งมุมของกรอบ */
    padding: 20px; /* เพิ่ม padding ภายในสไลด์ */
}

.carousel-buttons {
    position: absolute; /* จัดตำแหน่งปุ่ม */
    top: 50%; /* กำหนดให้อยู่ตรงกลาง */
    width: 100%; /* ให้ปุ่มเต็มความกว้าง */
    display: flex; /* ใช้ Flexbox สำหรับปุ่ม */
    justify-content: space-between; /* จัดปุ่มให้ห่างกัน */
    transform: translateY(-50%); /* เลื่อนขึ้นไปครึ่งหนึ่ง */
}

.carousel-button {
    border: none; /* ไม่มีกรอบ */
    border-radius: 5px; /* โค้งมุมของปุ่ม */
    cursor: pointer; /* แสดงว่าเป็นปุ่มคลิกได้ */
    padding: 10px; /* เพิ่ม padding ให้กับปุ่ม */
}

.carousel-button:hover {
    background-color: rgba(255, 255, 255, 1); /* เปลี่ยนสีเมื่อ hover */
}
.carousel-slide h2 {
    color: #000000; /* เปลี่ยนสีข้อความ */
    background-color: #f0f0f0; /* สีพื้นหลังของหมายเลขสไลด์ */
    padding: 10px; /* เพิ่มระยะห่างภายใน */
    border-radius: 5px; /* โค้งมุม */
    text-align: center; /* จัดกึ่งกลางข้อความ */
    margin-bottom: 15px; /* เพิ่มระยะห่างด้านล่าง */
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

<div class="checkpoint-list">
    <h2>จุดเช็คอิน</h2>
    <div class="checkpoint-items">
        <?php while ($checkpoint = $checkpoints_result->fetch_assoc()): ?>
            <div class="checkpoint-item">
                <img src="<?php echo $checkpoint['image_path'] ? $checkpoint['image_path'] : 'uploads/default.jpg'; ?>" alt="Checkpoint Image">
                <h3 class="checkpoint-title"><?php echo htmlspecialchars($checkpoint['title']); ?></h3>
                <p class="checkpoint-description"><?php echo htmlspecialchars($checkpoint['description']); ?></p>
            </div>
        <?php endwhile; ?>
    </div>
    <button class="styled-button" onclick="window.location.href='user_checkpoints.php'">รายละเอียด</button>
</div>

        <div class="carousel-container" id="carousel">
    <div class="carousel-slides">
        <div class="carousel-slide" style="background-color: #f0f0f0;">
            <h2>(1/3)</h2>
            <p><?php echo isset($texts[1]) ? $texts[1] : ''; ?></p>
            <form method="POST">
                <input type="hidden" name="slide_number" value="1">
            </form>
        </div>
        <div class="carousel-slide" style="background-color: #e0e0e0;">
            <h2>(2/3)</h2>
            <p><?php echo isset($texts[2]) ? $texts[2] : ''; ?></p>
            <form method="POST">
                <input type="hidden" name="slide_number" value="2">
            </form>
        </div>
        <div class="carousel-slide" style="background-color: #d0d0d0;">
            <h2>(3/3)</h2>
            <p><?php echo isset($texts[3]) ? $texts[3] : ''; ?></p>
            <form method="POST">
                <input type="hidden" name="slide_number" value="3">
            </form>
        </div>
    </div>

            <div class="carousel-buttons">
                <button class="carousel-button" onclick="prevSlide()">&#10094;</button>
                <button class="carousel-button" onclick="nextSlide()">&#10095;</button>
            </div>
        </div>
        <div class="video-section">
        <h2>วิดีโอแนะนำสถานที่ท่องเที่ยว</h2>

        <?php
        include 'db_connection.php';
        $result = $conn->query("SELECT video_url FROM videos WHERE id = 1");
        $row = $result->fetch_assoc();
        ?>

        <iframe src="<?php echo $row['video_url']; ?>" allowfullscreen></iframe>

         </div>

        </div>
        <button class="styled-button" onclick="window.location.href='user_article.php'">แสดงเพิ่มเติม</button>
    </div>
    <footer>
        <div class="footer-left">
            <img src="uploads/icons8-facebook-50.png" alt="Facebook">
            <img src="uploads/icons8-youtube-50.png" alt="YouTube">
            <img src="uploads/instagram.png" alt="Instagram">
            <img src="uploads/icons8-twitter-50.png" alt="Twitter">
        </div>

        <div class="footer-right">
            <?php
            include 'db_connection.php';
            $result = $conn->query("SELECT * FROM contact_info WHERE id = 1");
            $row = $result->fetch_assoc();
            ?>
            <h3>ติดต่อเรา</h3>
            <p>ที่อยู่: <?php echo $row['address']; ?></p>
            <p>เวลาเปิดบริการ: <?php echo $row['opening_hours']; ?></p>
            </form>
        </div>
    </footer>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            const content = document.getElementById("content");
            sidebar.classList.toggle("active");
            content.classList.toggle("shifted");
        }

        const lines = [
            "ถ้าคุณกำลังมองหาสินค้าที่กำลังหา...",
            "เรามีสินค้าหลากหลายรอคุณอยู่!",
            "สนใจสั่งซื้อได้ทันที"
        ];
        let lineIndex = 0;
        let charIndex = 0;

        function typeWriter() {
            if (lineIndex < lines.length) {
                const currentLine = lines[lineIndex];

                if (charIndex < currentLine.length) {
                    document.getElementById("text-container").innerHTML += currentLine.charAt(charIndex);
                    charIndex++;
                    setTimeout(typeWriter, 100);
                } else {
                    document.getElementById("text-container").innerHTML += "<br>";
                    charIndex = 0;
                    lineIndex++;
                    setTimeout(typeWriter, 500);
                }
            }
        }

        window.onload = typeWriter;

        let currentSlide = 0;

        function updateCarousel() {
            const slides = document.querySelector('.carousel-slides');
            slides.style.transform = `translateX(${-currentSlide * 100}%)`;
        }

        function nextSlide() {
            const slides = document.querySelector('.carousel-slides');
            currentSlide = (currentSlide + 1) % slides.children.length;
            updateCarousel();
        }

        function prevSlide() {
            const slides = document.querySelector('.carousel-slides');
            currentSlide = (currentSlide - 1 + slides.children.length) % slides.children.length;
            updateCarousel();
        }
    </script>
</body>
</html>
