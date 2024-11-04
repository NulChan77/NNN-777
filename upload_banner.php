<?php
$conn = new mysqli("localhost", "root", "", "user_management");

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่าไฟล์ถูกอัปโหลด
if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] === UPLOAD_ERR_OK) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["banner_image"]["name"]);
    move_uploaded_file($_FILES["banner_image"]["tmp_name"], $target_file);

    // ตั้งค่า `alt_text` ให้เป็นค่าว่างถ้าไม่มีข้อมูล
    $alt_text = isset($_POST['alt_text']) ? $_POST['alt_text'] : '';

    // แทรกข้อมูลแบนเนอร์ไปยังฐานข้อมูล
    $sql = "INSERT INTO banners (image_path, alt_text) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $_FILES["banner_image"]["name"], $alt_text);
    $stmt->execute();

    $stmt->close();
    header("Location: admin.php");
    exit();
} else {
    echo "Error uploading the file.";
}

$conn->close();
?>
