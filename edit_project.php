<?php
// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$connection = new mysqli("localhost", "root", "", "user_management");
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// ตรวจสอบว่าได้รับ project_id หรือไม่
if (isset($_GET['id'])) {
    $project_id = $_GET['id'];

    // ดึงข้อมูลโครงการจากฐานข้อมูล
    $project_sql = "SELECT * FROM projects WHERE id = $project_id";
    $project_result = $connection->query($project_sql);
    $project = $project_result->fetch_assoc();
}

// ตรวจสอบการส่งฟอร์มเพื่ออัปเดตข้อมูล
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    // ตรวจสอบว่ามีการอัปโหลดไฟล์ใหม่หรือไม่
    if (isset($_FILES['project_image']) && $_FILES['project_image']['error'] == 0) {
        $image_path = 'uploads/' . basename($_FILES['project_image']['name']);
        move_uploaded_file($_FILES['project_image']['tmp_name'], $image_path);

        // อัปเดตข้อมูลโครงการในฐานข้อมูล
        $connection->query("UPDATE projects SET title = '$title', description = '$description', image_path = '$image_path' WHERE id = $project_id");
    } else {
        // อัปเดตข้อมูลโดยไม่เปลี่ยนแปลงภาพ
        $connection->query("UPDATE projects SET title = '$title', description = '$description' WHERE id = $project_id");
    }

    // เปลี่ยนเส้นทางไปยังหน้าหลักหลังจากอัปเดต
    header("Location: layout.php"); // เปลี่ยนเส้นทางไปยังหน้าหลักที่คุณต้องการ
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>แก้ไขโครงการ</title>
</head>
<body>
<header>
    <h1>แก้ไขโครงการ</h1>
</header>

<div class="container">
    <?php if ($project): ?>
        <form method="POST" enctype="multipart/form-data">
            <label for="title">ชื่อโครงการ:</label>
            <input type="text" id="title" name="title" value="<?php echo $project['title']; ?>" required>
            <label for="description">คำอธิบาย:</label>
            <textarea id="description" name="description" rows="4" required><?php echo $project['description']; ?></textarea>
            <label for="project_image">รูปภาพใหม่ (ถ้ามี):</label>
            <input type="file" id="project_image" name="project_image" accept="image/*">
            <input type="submit" value="อัปเดตโครงการ">
        </form>
    <?php else: ?>
        <p>ไม่พบโครงการที่ต้องการแก้ไข</p>
    <?php endif; ?>
</div>

<footer>
    <p>ข้อมูลติดต่อ: อีเมล info@example.com | โทร 012-345-6789</p>
</footer>

</body>
</html>

<?php
// ปิดการเชื่อมต่อฐานข้อมูล
$connection->close();
?>
