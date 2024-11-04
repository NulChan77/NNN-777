<?php
$connection = new mysqli("localhost", "root", "", "user_management");

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $type = $_POST['type']; // 'team' หรือ 'project' ขึ้นอยู่กับส่วนที่แก้ไข

    // ตรวจสอบว่ามีการอัปโหลดรูปภาพใหม่หรือไม่
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $fileName;
        
        // ย้ายไฟล์ที่อัปโหลดไปยังโฟลเดอร์ uploads
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            $imagePath = $targetFilePath;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Image upload failed']);
            exit;
        }
    } else {
        $imagePath = null;
    }

    // อัปเดตข้อมูลในฐานข้อมูล
    if ($type == 'team') {
        $sql = "UPDATE team_members SET name = ?, description = ?";
        $sql .= $imagePath ? ", image_path = ?" : "";
        $sql .= " WHERE id = ?";
    } else if ($type == 'project') {
        $sql = "UPDATE projects SET title = ?, description = ?";
        $sql .= $imagePath ? ", image_path = ?" : "";
        $sql .= " WHERE id = ?";
    }

    $stmt = $connection->prepare($sql);
    if ($imagePath) {
        $stmt->bind_param("sssi", $name, $description, $imagePath, $id);
    } else {
        $stmt->bind_param("ssi", $name, $description, $id);
    }

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Data updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Data update failed']);
    }

    $stmt->close();
}

$connection->close();
?>
