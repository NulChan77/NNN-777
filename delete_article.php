<?php
include 'db_connect.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่ามีการส่ง id มาจาก URL หรือไม่
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // ลบบทความ
    $sql = "DELETE FROM articles WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    
    // ลบภาพที่เกี่ยวข้อง (ถ้ามี)
    $sql_images = "DELETE FROM article_images WHERE article_id = :article_id";
    $stmt_images = $pdo->prepare($sql_images);
    $stmt_images->execute(['article_id' => $id]);
    
    // เปลี่ยนเส้นทางไปยังหน้าบทความทั้งหมด
    header("Location: article.php");
    exit();
} else {
    echo "<p>Invalid article ID.</p>";
    exit();
}
?>
