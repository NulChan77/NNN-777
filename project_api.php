<?php
session_start();
include 'db_connection.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบว่ามีข้อมูลโครงการหรือไม่
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $image_path = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // อัพโหลดไฟล์ภาพ
        $image = $_FILES['image'];
        $uploadDir = 'uploads/';
        $image_path = $uploadDir . basename($image['name']);

        if (!move_uploaded_file($image['tmp_name'], $image_path)) {
            $response['message'] = 'ไม่สามารถอัพโหลดไฟล์ภาพได้';
            echo json_encode($response);
            exit();
        }
    }

    // เชื่อมต่อฐานข้อมูลและบันทึกข้อมูล
    $stmt = $conn->prepare("INSERT INTO projects (name, description, image_path) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $description, $image_path);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'บันทึกข้อมูลโครงการเรียบร้อยแล้ว';
    } else {
        $response['message'] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
    }
    $stmt->close();
    $conn->close();
}

echo json_encode($response);
?>
