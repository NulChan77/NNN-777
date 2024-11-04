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
    <title>Dashboard</title>

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
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
