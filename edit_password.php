<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'user_management');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่ามีการส่ง user_id มาหรือไม่
if (!isset($_GET['id'])) {
    header('Location: user_list.php');
    exit;
}

$user_id = $_GET['id'];

// แก้ไขรหัสผ่าน
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // ตรวจสอบว่ารหัสผ่านใหม่และการยืนยันรหัสผ่านตรงกันหรือไม่
    if ($new_password === $confirm_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hashed_password, $user_id);

        if ($stmt->execute()) {
            header('Location: user_list.php');
            exit;
        } else {
            echo "Error updating password.";
        }
    } else {
        echo "Passwords do not match."; // แสดงข้อความเมื่อรหัสผ่านไม่ตรงกัน
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Password</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            background-color: #fff;
            padding: 40px; /* เพิ่ม padding ให้กับคอนเทนเนอร์ */
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px; /* กำหนดความกว้างสูงสุด */
            margin: auto; /* จัดกลาง */
        }
        input[type="password"],
        button {
            width: 100%; /* ให้ช่อง input ขยายเต็มความกว้าง */
            padding: 12px; /* เพิ่ม padding สำหรับช่อง input */
            margin: 10px 0; /* เพิ่ม margin ระหว่าง input */
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #007bff; /* สีพื้นหลังของปุ่ม */
            color: white; /* สีข้อความของปุ่ม */
            cursor: pointer; /* แสดงว่าเป็นปุ่มคลิกได้ */
        }
        button:hover {
            background-color: #0056b3; /* สีพื้นหลังเมื่อ hover */
        }
        .back-link {
            display: block; /* ทำให้ปุ่ม Back เป็นบล็อค */
            text-align: center; /* จัดกลางข้อความ */
            margin-top: 20px; /* เพิ่ม margin บน */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Password</h2>
        <form method="POST">
            <input type="password" name="new_password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
            <button type="submit">Save Changes</button>
        </form>
        <a href="user_list.php" class="back-link"><button>Back to User List</button></a>
    </div>
</body>
</html>
