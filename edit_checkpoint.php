<?php
// เชื่อมต่อฐานข้อมูล
include 'db_connection.php';

// ตรวจสอบว่ามีการส่ง ID จุดเช็คอินมาหรือไม่
if (!isset($_GET['id'])) {
    echo "ไม่มีการระบุ ID ของจุดเช็คอิน";
    exit;
}

$id = $_GET['id'];

// ดึงข้อมูลจุดเช็คอินที่ต้องการแก้ไข
$result = $conn->query("SELECT * FROM checkpoints WHERE id = $id");
$checkpoint = $result->fetch_assoc();

// ตรวจสอบการส่งแบบฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $image_path = $checkpoint['image_path']; // ค่าภาพเริ่มต้น
    
    // ตรวจสอบว่ามีการอัปโหลดไฟล์ใหม่หรือไม่
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        
        // ตรวจสอบการอัปโหลดไฟล์
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        } else {
            echo "เกิดข้อผิดพลาดในการอัปโหลดไฟล์";
        }
    }

    // อัปเดตข้อมูลในฐานข้อมูล
    $stmt = $conn->prepare("UPDATE checkpoints SET title = ?, description = ?, image_path = ? WHERE id = ?");
    $stmt->bind_param("sssi", $title, $description, $image_path, $id);

    if ($stmt->execute()) {
        echo "แก้ไขจุดเช็คอินเรียบร้อยแล้ว";
        header("Location: checkpoint_list.php"); // ย้อนกลับไปหน้ารายการจุดเช็คอิน
        exit;
    } else {
        echo "เกิดข้อผิดพลาด: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขจุดเช็คอิน</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            font-weight: bold;
            color: #333;
            margin-top: 10px;
            display: block;
        }
        input[type="text"], textarea, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            background-color: #4caf50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
            font-size: 16px;
            width: 100%;
        }
        button:hover {
            background-color: #45a049;
        }
        a {
            display: inline-block;
            margin-top: 10px;
            text-align: center;
            text-decoration: none;
            color: #f44336;
        }
        a:hover {
            text-decoration: underline;
        }
        img {
            margin-top: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<h2>แก้ไขจุดเช็คอิน</h2>

<form action="" method="POST" enctype="multipart/form-data">
    <label for="title">ชื่อจุดเช็คอิน:</label>
    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($checkpoint['title']); ?>" required>
    
    <label for="description">รายละเอียด:</label>
    <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($checkpoint['description']); ?></textarea>
    
    <label for="image">ภาพจุดเช็คอิน (อัปโหลดใหม่เพื่อเปลี่ยน):</label>
    <img src="<?php echo $checkpoint['image_path'] ? $checkpoint['image_path'] : 'uploads/default.jpg'; ?>" alt="Checkpoint Image" width="150">
    <input type="file" id="image" name="image">
    
    <button type="submit">บันทึกการเปลี่ยนแปลง</button>
    <a href="checkpoint_list.php">ยกเลิก</a>
</form>

</body>
</html>
