<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'user_management');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบการส่งข้อมูลจากฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $gmail = $_POST['gmail'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // สร้างคำสั่ง SQL เพื่อเพิ่มผู้ใช้ใหม่
    $sql = "INSERT INTO users (firstname, lastname, gmail, password, phone) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error preparing the statement: " . $conn->error);
    }

    $stmt->bind_param("sssss", $firstname, $lastname, $gmail, $hashed_password, $phone);

    if ($stmt->execute()) {
        header('Location: user_list.php'); // เปลี่ยนไปยังหน้า User List
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Add New User</title>
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
            max-width: 500px; /* กำหนดความกว้างสูงสุด */
            margin: auto; /* จัดกลาง */
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        button {
            width: 100%; /* ให้ช่อง input ขยายเต็มความกว้าง */
            padding: 12px; /* เพิ่ม padding สำหรับช่อง input */
            margin: 10px 0; /* เพิ่ม margin ระหว่าง input */
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #28a745; /* สีพื้นหลังของปุ่ม */
            color: white; /* สีข้อความของปุ่ม */
            cursor: pointer; /* แสดงว่าเป็นปุ่มคลิกได้ */
        }
        button:hover {
            background-color: #218838; /* สีพื้นหลังเมื่อ hover */
        }
    </style>
</head>
<body>
<div class="container">
        <h2>Add User</h2>
        <form method="POST" action="add_user.php">
            <input type="text" name="firstname" placeholder="First Name" required>
            <input type="text" name="lastname" placeholder="Last Name" required>
            <input type="email" name="gmail" placeholder="Email" required>
            <input type="text" name="phone" placeholder="Phone" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Add User</button>
        </form>
        <a href="user_list.php"><button class="back-button">Back to User List</button></a>
    </div>
</body>
</html>
