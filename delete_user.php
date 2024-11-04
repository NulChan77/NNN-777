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

// ลบข้อมูลผู้ใช้
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $delete_user_id = $_POST['delete_user_id'];

    // ป้องกันการลบตัวเอง
    if ($delete_user_id == $_SESSION['user_id']) {
        echo "You cannot delete your own account!";
    } else {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $delete_user_id);

        if ($stmt->execute()) {
            echo "User deleted successfully!";
        } else {
            echo "Error deleting user.";
        }
    }

    header('Location: user_list.php');
    exit;
}

$stmt->close();
$conn->close();
?>
