<?php
// สร้างการเชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "", "user_management");

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับ ID ของสถานที่ท่องเที่ยวที่ต้องการลบ
if (isset($_GET['id'])) {
    $tourist_id = intval($_GET['id']);

    // ดึงชื่อไฟล์ภาพจากฐานข้อมูล
    $result = $conn->query("SELECT image FROM tourist_destinations WHERE id=$tourist_id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $image_path = $row['image'];

        // ลบภาพจากโฟลเดอร์
        if (file_exists($image_path)) {
            unlink($image_path);
        }

        // ลบข้อมูลจากฐานข้อมูล
        $delete_sql = "DELETE FROM tourist_destinations WHERE id=$tourist_id";
        if ($conn->query($delete_sql) === TRUE) {
            echo "ลบข้อมูลสำเร็จ!";
        } else {
            echo "เกิดข้อผิดพลาดในการลบข้อมูล: " . $conn->error;
        }
    } else {
        echo "ไม่พบข้อมูลสถานที่ท่องเที่ยว";
    }
}

$conn->close();
?>
