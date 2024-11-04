<?php
session_start();
require_once 'db_connection.php'; // เชื่อมต่อฐานข้อมูล

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์ม
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    // ตรวจสอบว่าข้อมูลไม่ว่าง
    if (!empty($name) && !empty($email) && !empty($message)) {
        // เตรียม SQL สำหรับบันทึกข้อมูล
        $sql = "INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            // Bind parameters
            $stmt->bind_param("sss", $name, $email, $message);
            // Execute query
            if ($stmt->execute()) {
                // ประสบความสำเร็จ
                header("Location: user_contact.php?success=1"); // เปลี่ยนเส้นทางหลังส่งข้อความสำเร็จ
                exit();
            } else {
                // เกิดข้อผิดพลาดในการบันทึกข้อมูล
                echo "เกิดข้อผิดพลาด: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "เกิดข้อผิดพลาดในการเตรียม SQL: " . $conn->error;
        }
    } else {
        echo "กรุณากรอกข้อมูลให้ครบถ้วน.";
    }
}

$conn->close();
?>
