<?php
session_start();

// ลบข้อมูล session ทั้งหมด
session_unset();
session_destroy();

// ป้องกันการย้อนกลับไปหน้าเดิมหลังจาก logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// เปลี่ยนเส้นทางไปที่หน้า index.php
header("Location: index.php");
exit;
?>
<script>
    // ล้าง sessionStorage และ localStorage หลังจากล็อกเอาต์
    sessionStorage.clear();
    localStorage.clear();
</script>
