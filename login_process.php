<?php
session_start();

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'user_management');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $gmail = $_POST['gmail'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE gmail = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $gmail);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['logged_in'] = true;
            header('Location: dashboard.php');
            exit;
        } else {
            echo "Invalid password.";
        }
    } else {
        // ถ้าไม่มีบัญชี ให้ส่งพารามิเตอร์ไปที่หน้า login.php
        header('Location: login.php?error=noaccount');
        exit;
    }

    $stmt->close();
}

$conn->close();
?>
