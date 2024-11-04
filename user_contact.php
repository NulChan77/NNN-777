<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'db_connection.php'; // Assuming a file for database connection

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
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <title>Contact Us</title>
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

        <!-- Profile Dropdown -->
        <div class="profile" onclick="toggleDropdown()">
            <div class="profile-icon">
                <?php echo strtoupper(htmlspecialchars(substr($user['firstname'], 0, 1))); ?>
            </div>
            <div class="dropdown-menu" id="dropdownMenu">
                <div>Username: <?php echo htmlspecialchars($user['username']); ?></div>
                <a href="edit_profile.php">Edit Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("active");
            const expanded = sidebar.classList.contains("active");
            document.querySelector('.toggle-btn').setAttribute('aria-expanded', expanded);
        }

        function toggleDropdown() {
            const dropdown = document.getElementById("dropdownMenu");
            dropdown.classList.toggle("show");
        }

        window.onclick = function(event) {
            if (!event.target.matches('.profile, .profile *')) {
                const dropdown = document.getElementById("dropdownMenu");
                if (dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                }
            }
        };
    </script>
    <style>

/* Global Styles */
body {
    font-family: 'Itim', cursive;
    background: #fff;
    margin: 0;
    padding: 0;
    text-align: center;
}

h2, h3 {
    color: #333;
}

/* Sidebar Styles */
.sidebar {
    position: fixed;
    top: 0;
    left: -250px; /* Sidebar hidden initially */
    width: 250px;
    height: 100%;
    background-color: #222;
    padding: 20px;
    transition: left 0.3s ease;
    z-index: 100;
}

.sidebar.active {
    left: 0; /* Slide-in sidebar */
}

.sidebar h2 {
    color: white;
    text-align: center;
    margin-bottom: 20px;
}

.sidebar ul {
    list-style-type: none;
    padding: 0;
}

.sidebar ul li {
    margin-bottom: 15px;
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


.toggle-btn {
    position: fixed;
    left: 10px;
    top: 10px;
    background-color: #222;
    color: white;
    padding: 8px;
    cursor: pointer;
    border: none;
    z-index: 101;
}

/* Main Content */
.content {
    transition: margin-left 0.3s ease;
    margin-left: 0;
    padding: 20px;
}

.content.shifted {
    margin-left: 250px; /* Shift content when sidebar is open */
}

/* Form Container */
.form-container {
    margin: 20px auto;
    width: 90%; /* ขยายความกว้างฟอร์ม */
    max-width: 800px; /* กำหนดขนาดสูงสุดที่ใหญ่ขึ้น */
    background: #fff;
    padding: 30px; /* เพิ่ม padding */
    border-radius: 8px; /* มุมโค้งนิดหน่อย */
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.form-container input[type="text"],
.form-container input[type="email"],
.form-container textarea {
    width: 100%; /* ทำให้ input และ textarea กว้างเต็มฟอร์ม */
    padding: 15px; /* เพิ่ม padding ภายใน */
    margin-bottom: 15px; /* เพิ่มช่องว่างระหว่าง input */
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
    resize: vertical; /* อนุญาตให้ปรับขนาด textarea เฉพาะแนวตั้ง */
}

.form-container button {
    width: 100%; /* ปุ่มกว้างเต็มฟอร์ม */
    padding: 15px;
    font-size: 18px;
    background-color: #00796b;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.form-container button:hover {
    background-color: #004d40;
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

/* Contact Information */
.contact-info {
    margin: 30px auto;
    width: 90%; /* ขยายความกว้าง */
    max-width: 800px; /* กำหนดขนาดสูงสุด */
    background: #fff;
    padding: 30px; /* เพิ่ม padding */
    border-radius: 8px; /* มุมโค้ง */
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    text-align: left;
    font-size: 18px; /* ขนาดตัวอักษรใหญ่ขึ้น */
    line-height: 1.6; /* เพิ่มระยะห่างระหว่างบรรทัด */
}

.contact-info h3 {
    font-size: 24px; /* ขนาดตัวอักษรหัวข้อใหญ่ขึ้น */
    margin-bottom: 20px;
    color: #333;
}

.contact-info p {
    margin-bottom: 15px;
}

.contact-info strong {
    color: #00796b; /* เพิ่มสีเน้นให้กับหัวข้อ */
}


/* Map Container */
.map-container {
    margin: 30px auto;
    width: 90%; /* ขยายความกว้าง */
    max-width: 800px; /* กำหนดขนาดสูงสุด */
    padding: 30px; /* เพิ่ม padding รอบแผนที่ */
    background: #fff;
    border-radius: 8px; /* มุมโค้ง */
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    text-align: center;
    font-size: 18px; /* ขนาดตัวอักษรใหญ่ขึ้น */
}

.map-container h3 {
    font-size: 24px; /* ขนาดหัวข้อใหญ่ขึ้น */
    margin-bottom: 20px;
    color: #333;
}


/* Profile Section with Dropdown */
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
    width: 200px;
    z-index: 10;
}

.dropdown-menu.show {
    display: block; /* Show dropdown on toggle */
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

/* Button Styles */
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

/* Responsive Design */
@media (max-width: 768px) {
    .sidebar {
        width: 200px;
    }
    
    .content.shifted {
        margin-left: 200px;
    }
    
    .form-container, .contact-info, .map-container {
        width: 90%;
    }
}


    </style>
    <!-- Check for success message -->
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div style="color: green; text-align: center; margin: 20px 0;">ส่งข้อความเรียบร้อยแล้ว!</div>
    <?php endif; ?>
<div class="contact-info">
    <h3>ข้อมูลติดต่อ</h3>
    <p><strong>อีเมล:</strong> dee055909@gmail.com</p>
    <p><strong>เบอร์โทร:</strong> +66 80 147 5044</p>
    <p><strong>ที่อยู่:</strong> ประจวบคีรีขันธ์ 77110 อำเภอหัวหิน ตำบล หนองแก</p>
</div>

<div class="form-container">
    <h2>ติดต่อเรา</h2>
    <form action="send_contact.php" method="post">
        <input type="text" name="name" placeholder="ชื่อ" required><br><br>
        <input type="email" name="email" placeholder="อีเมล" required><br><br>
        <textarea name="message" placeholder="ข้อความ" required></textarea><br><br>
        <button type="submit" class="styled-button">ส่งข้อความ</button>
    </form>
</div>

<div class="map-container">
    <h3>ที่อยู่ของเรา</h3>
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d62323.22815515674!2d99.91703137166367!3d12.502782069260228!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30fdac42d71b5507%3A0x40223bc2c381270!2z4LiV4Liz4Lia4LilIOC4q-C4meC4reC4h-C5geC4gSDguK3guLPguYDguKDguK3guKvguLHguKfguKvguLTguJkg4Lib4Lij4Liw4LiI4Lin4Lia4LiE4Li14Lij4Li14LiC4Lix4LiZ4LiY4LmMIDc3MTEw!5e0!3m2!1sth!2sth!4v1730579372521!5m2!1sth!2sth" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</div>
</body>
</html>
