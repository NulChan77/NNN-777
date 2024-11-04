<?php
require_once 'config.php';
header('Content-Type: application/json');

// ฟังก์ชันสำหรับอัพโหลดรูปภาพ
function uploadImage($file, $memberId) {
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $target_file = $target_dir . $memberId . "_" . time() . "." . $imageFileType;
    
    // ตรวจสอบว่าเป็นไฟล์รูปภาพจริง
    if(getimagesize($file["tmp_name"]) === false) {
        return false;
    }
    
    // ตรวจสอบนามสกุลไฟล์
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        return false;
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $target_file;
    }
    return false;
}

// ดึงข้อมูลสมาชิกทั้งหมด
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT * FROM team_members ORDER BY id");
    $stmt->execute();
    $result = $stmt->get_result();
    $members = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode(['success' => true, 'data' => $members]);
    $stmt->close();
    $conn->close();
}

// อัพเดตข้อมูลสมาชิก
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = connectDB();
    
    $memberId = $_POST['id'];
    $name = $_POST['name'];
    $position = $_POST['position'];
    $description = $_POST['description'];
    
    // ถ้ามีการอัพโหลดรูปภาพใหม่
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image_path = uploadImage($_FILES['image'], $memberId);
        if ($image_path === false) {
            echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
            exit;
        }
        
        // อัพเดตข้อมูลพร้อมรูปภาพ
        $stmt = $conn->prepare("UPDATE team_members SET name=?, position=?, description=?, image_path=? WHERE id=?");
        $stmt->bind_param("sssss", $name, $position, $description, $image_path, $memberId);
    } else {
        // อัพเดตข้อมูลโดยไม่เปลี่ยนรูปภาพ
        $stmt = $conn->prepare("UPDATE team_members SET name=?, position=?, description=? WHERE id=?");
        $stmt->bind_param("ssss", $name, $position, $description, $memberId);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Member updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update member']);
    }
    
    $stmt->close();
    $conn->close();
}