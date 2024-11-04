<?php
session_start();

// Set up database connection
$connection = new mysqli("localhost", "root", "", "user_management");
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Handle Add Team Member Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_team_member'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $image_path = 'uploads/' . basename($_FILES['image']['name']);

    // Move the uploaded file to the uploads directory
    if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
        // Use a prepared statement for security
        $stmt = $connection->prepare("INSERT INTO team_members (name, description, image_path) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $description, $image_path);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Error uploading file.";
    }
}

// Handle Add Project Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_project'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $image_path = 'uploads/' . basename($_FILES['project_image']['name']);

    // Move the uploaded file to the uploads directory
    if (move_uploaded_file($_FILES['project_image']['tmp_name'], $image_path)) {
        // Use a prepared statement for security
        $stmt = $connection->prepare("INSERT INTO projects (title, description, image_path) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $description, $image_path);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Error uploading file.";
    }
}

// Handle Delete Action for Team Member
if (isset($_GET['delete_team_member_id'])) {
    $delete_id = $_GET['delete_team_member_id'];
    $stmt = $connection->prepare("DELETE FROM team_members WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
}

// Handle Delete Action for Project
if (isset($_GET['delete_project_id'])) {
    $delete_id = $_GET['delete_project_id'];
    $stmt = $connection->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
}

// Retrieve Team Members
$team_sql = "SELECT * FROM team_members";
$team_result = $connection->query($team_sql);

// Retrieve Projects
$project_sql = "SELECT * FROM projects";
$project_result = $connection->query($project_sql);

// Close the database connection
$connection->close();
?>



<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <title>ข่าวสาร</title>
    <style>
        /* Global Styles */
        body {
            font-family: 'Itim', sans-serif; /* Updated to use the Itim font */
            background-color: #f4f4f4;
            background: linear-gradient(to right, #e0f2f1 50%, #ffffff 50%);
            margin: 0;
            padding: 0;
        }
        header, footer {
            background: #004d40;
            color: white;
            text-align: center;
            padding: 15px 0;
        }
        h1, h2 {
            margin: 0;
        }

        /* Container for the Content */
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Team and Project Sections */
        .section-container {
            margin-bottom: 40px;
            position: relative;
            padding-top: 40px;
        }
        
        .section-header {
            font-size: 24px;
            color: #004d40;
            text-align: center;
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            padding: 0 10px;
            margin-top: -20px;
        }

        .team-section, .project-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .team-member, .project {
            background: #fafafa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            position: relative;
        }
        .team-member:hover, .project:hover {
            transform: scale(1.05);
        }
        .team-member img, .project img {
            width: 100%;
            height: auto;
            max-height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .add-new {
            text-align: center;
            margin: 20px 0;
        }
        .add-new a {
            background-color: #00796b;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .add-new a:hover {
            background-color: #005f56;
        }

        /* Add form styling */
        form {
            margin-top: 20px;
            padding: 15px;
            background: #e0f2f1;
            border-radius: 8px;
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], textarea, input[type="file"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            background-color: #00796b;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
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

        .button-container {
    display: flex; /* Use flexbox for alignment */
    justify-content: center; /* Center the button horizontally */
    align-items: center; /* Center the button vertically (if needed) */
    height: 100%; /* Make sure the container takes full height if needed */
    margin-top: 20px; /* Optional: Add space above */
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
<header>
    <h1>ข่าวสารและกิจกรรม</h1>
</header>

<div class="container">
    <!-- Team Section -->
    <section class="section-container">
        <h2 class="section-header">กิจกรรม</h2>
        <div class="team-section">
            <?php if ($team_result->num_rows > 0): ?>
                <?php while ($team_member = $team_result->fetch_assoc()): ?>
                    <div class="team-member">
                        <img src="<?php echo $team_member['image_path']; ?>" alt="<?php echo $team_member['name']; ?>">
                        <h3><?php echo $team_member['name']; ?></h3>
                        <p><?php echo $team_member['description']; ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; color: #777;">ยังไม่มีกิจกรรม</p>
            <?php endif; ?>
        </div>


    </section>

    <!-- Project Section -->
    <section class="section-container">
        <h2 class="section-header">ข่าวสาร</h2>
        <div class="project-section">
            <?php if ($project_result->num_rows > 0): ?>
                <?php while ($project = $project_result->fetch_assoc()): ?>
                    <div class="project">
                        <img src="<?php echo $project['image_path']; ?>" alt="<?php echo $project['title']; ?>">
                        <h3><?php echo $project['title']; ?></h3>
                        <p><?php echo $project['description']; ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; color: #777;">ยังไม่มีข่าวสาร</p>
            <?php endif; ?>
        </div>
    </section>
</div>
<footer>
    <p>ข้อมูลติดต่อ: อีเมล info@example.com | โทร 012-345-6789</p>
</footer>

</body>
</html>