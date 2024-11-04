<?php
// เริ่มต้นการใช้งาน session
session_start();

// ตรวจสอบว่าได้เชื่อมต่อฐานข้อมูลหรือยัง
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_management"; // เปลี่ยนเป็นชื่อฐานข้อมูลของคุณ

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่ามีการส่ง ID ของ checkpoint ที่ต้องการลบหรือไม่
if (isset($_GET['id'])) {
    $checkpoint_id = $_GET['id'];

    // ค้นหารูปภาพที่เกี่ยวข้องกับ checkpoint นี้เพื่อลบไฟล์
    $sql = "SELECT image_path FROM checkpoints WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $checkpoint_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $image_path = $row['image_path'];

        // ลบไฟล์รูปภาพจากโฟลเดอร์ ถ้ามีรูปอยู่จริง
        if (file_exists($image_path)) {
            unlink($image_path);
        }

        // ลบข้อมูล checkpoint จากฐานข้อมูล
        $sql_delete = "DELETE FROM checkpoints WHERE id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $checkpoint_id);

        if ($stmt_delete->execute()) {
            $_SESSION['message'] = "Checkpoint deleted successfully.";
        } else {
            $_SESSION['message'] = "Error deleting checkpoint.";
        }

        $stmt_delete->close();
    } else {
        $_SESSION['message'] = "Checkpoint not found.";
    }

    $stmt->close();
} else {
    $_SESSION['message'] = "No checkpoint ID provided.";
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();

// กลับไปยังหน้าหลักหรือหน้าแสดง checkpoint หลังจากลบเสร็จสิ้น
header("Location: checkpoint_list.php"); // เปลี่ยนเป็นหน้าที่คุณต้องการกลับไป
exit();
?>
