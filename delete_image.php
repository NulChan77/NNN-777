<?php
if (isset($_GET['tourist_id'])) {
    $tourist_id = $_GET['tourist_id'];

    // รหัสการเชื่อมต่อฐานข้อมูลจะอยู่ที่นี่...

    // รับที่อยู่ภาพปัจจุบันจากฐานข้อมูล
    $sql = "SELECT image_path FROM tourist_places WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $tourist_id);
    $stmt->execute();
    $stmt->bind_result($image_path);
    $stmt->fetch();
    $stmt->close();

    // ลบภาพจากเซิร์ฟเวอร์
    if (file_exists($image_path)) {
        unlink($image_path);
    }

    // ลบการอ้างอิงภาพจากฐานข้อมูล
    $sql = "UPDATE tourist_places SET image_path = NULL, description = NULL WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $tourist_id);
    $stmt->execute();
    $stmt->close();

    echo "ลบรูปภาพเรียบร้อยแล้ว.";
}
?>
