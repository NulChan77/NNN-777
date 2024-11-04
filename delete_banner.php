<?php
$conn = new mysqli("localhost", "root", "", "user_management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับค่า id ของแบนเนอร์
$banner_id = $_POST['banner_id'];

// ดึงข้อมูลแบนเนอร์เพื่อลบไฟล์รูป
$sql = "SELECT image_path FROM banners WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $banner_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $banner = $result->fetch_assoc();
    $image_path = "uploads/" . $banner['image_path'];
    
    // ลบไฟล์รูป
    if (file_exists($image_path)) {
        unlink($image_path);
    }

    // ลบข้อมูลแบนเนอร์ออกจากฐานข้อมูล
    $delete_sql = "DELETE FROM banners WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $banner_id);
    $delete_stmt->execute();
}

// ปิดการเชื่อมต่อฐานข้อมูล
$stmt->close();
$conn->close();

header("Location: admin.php");
exit();
?>
