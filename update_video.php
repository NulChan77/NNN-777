<?php
include 'db_connection.php';

$video_url = $_POST['video_url'];

// ตรวจสอบว่าลิงก์เป็นแบบ embed ของ YouTube หรือไม่
if (strpos($video_url, "youtube.com/embed/") === false) {
    die("โปรดใส่ลิงก์ YouTube แบบ embed เช่น https://www.youtube.com/embed/...");
}

// อัปเดตลิงก์ในฐานข้อมูล
$stmt = $conn->prepare("UPDATE videos SET video_url = ? WHERE id = 1");
$stmt->bind_param("s", $video_url);
$stmt->execute();
$stmt->close();

header("Location: index.php");
?>
