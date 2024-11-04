<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to index.php
    header("Location: index.php");
    exit();
}

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

// Retrieve User Information
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

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
        /* Delete Button Styling */
        .delete-button {
            background-color: #d32f2f;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
            position: absolute;
            top: 10px;
            right: 10px;
            transition: background-color 0.3s;
        }
        .delete-button:hover {
            background-color: #b71c1c;
        }
    </style>
</head>
<body>
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
                        <a class="delete-button" href="?delete_team_member_id=<?php echo $team_member['id']; ?>" onclick="return confirm('คุณต้องการลบสมาชิกนี้หรือไม่?');">ลบ</a>
                        <img src="<?php echo $team_member['image_path']; ?>" alt="<?php echo $team_member['name']; ?>">
                        <h3><?php echo $team_member['name']; ?></h3>
                        <p><?php echo $team_member['description']; ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; color: #777;">ยังไม่มีกิจกรรม</p>
            <?php endif; ?>
        </div>

        <!-- Add New Member Form -->
        <form method="POST" enctype="multipart/form-data">
            <h3>เพิ่มกิจกรรม</h3>
            <label for="name">ชื่อกิจกรรม:</label>
            <input type="text" id="name" name="name" required>
            <label for="description">คำอธิบาย:</label>
            <textarea id="description" name="description" rows="4" required></textarea>
            <label for="image">รูปภาพ:</label>
            <input type="file" id="image" name="image" accept="image/*" required>
            <input type="hidden" name="add_team_member" value="1">
            <input type="submit" value="เพิ่มกิจกรรม">
        </form>
    </section>

    <!-- Project Section -->
    <section class="section-container">
        <h2 class="section-header">ข่าวสาร</h2>
        <div class="project-section">
            <?php if ($project_result->num_rows > 0): ?>
                <?php while ($project = $project_result->fetch_assoc()): ?>
                    <div class="project">
                        <a class="delete-button" href="?delete_project_id=<?php echo $project['id']; ?>" onclick="return confirm('คุณต้องการลบโครงการนี้หรือไม่?');">ลบ</a>
                        <img src="<?php echo $project['image_path']; ?>" alt="<?php echo $project['title']; ?>">
                        <h3><?php echo $project['title']; ?></h3>
                        <p><?php echo $project['description']; ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; color: #777;">ยังไม่มีข่าวสาร</p>
            <?php endif; ?>
        </div>

        <!-- Add New Project Form -->
        <form method="POST" enctype="multipart/form-data">
            <h3>เพิ่มข่าวสารใหม่</h3>
            <label for="title">ชื่อข่าวสาร:</label>
            <input type="text" id="title" name="title" required>
            <label for="description">คำอธิบาย:</label>
            <textarea id="description" name="description" rows="4" required></textarea>
            <label for="project_image">รูปภาพ:</label>
            <input type="file" id="project_image" name="project_image" accept="image/*" required>
            <input type="hidden" name="add_project" value="1">
            <input type="submit" value="เพิ่มโครงการ">
        </form>
    </section>
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

<footer>
    <p>ข้อมูลติดต่อ: อีเมล info@example.com | โทร 012-345-6789</p>
</footer>

</body>
</html>

<?php
