<?php
session_start();

// ตรวจสอบว่าเข้าสู่ระบบหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "", "user_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// เพิ่มข้อมูลจุดเช็คอินใหม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);

    // อัปโหลดรูปภาพ
    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
        $target_dir = "uploads/";
        $image_path = $target_dir . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {
            // หากอัปโหลดสำเร็จ เพิ่มข้อมูลในฐานข้อมูล
            $sql = "INSERT INTO checkpoints (title, description, image_path) VALUES ('$title', '$description', '$image_path')";
            if ($conn->query($sql) === TRUE) {
                echo "<p style='color: green;'>เพิ่มจุดเช็คอินเรียบร้อยแล้ว</p>";
            } else {
                echo "<p style='color: red;'>Error: " . $sql . "<br>" . $conn->error . "</p>";
            }
        } else {
            echo "<p style='color: red;'>การอัปโหลดรูปภาพล้มเหลว</p>";
        }
    } else {
        echo "<p style='color: red;'>กรุณาเลือกไฟล์รูปภาพ</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มจุดเช็คอิน</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f9f9f9; 
            text-align: center; 
            margin: 0; 
            padding: 20px; 
        }
        .form-container { 
            margin: 20px auto; 
            width: 90%; 
            max-width: 500px; 
            background: #fff; 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); 
        }
        h2 { 
            color: #00796b; 
        }
        input[type="text"], textarea { 
            width: 100%; 
            padding: 10px; 
            margin: 10px 0; 
            border: 1px solid #ccc; 
            border-radius: 5px; 
        }
        input[type="file"] { 
            margin: 10px 0; 
        }
        .styled-button { 
            margin-top: 10px; 
            padding: 10px 20px; 
            font-size: 16px; 
            background-color: #00796b; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            transition: background-color 0.3s ease; 
        }
        .styled-button:hover { 
            background-color: #004d40; 
        }
        .back-link { 
            display: inline-block; 
            margin-top: 20px; 
            text-decoration: none; 
            color: #00796b; 
        }
        .back-link:hover { 
            text-decoration: underline; 
        }
        img { 
            max-width: 100%; 
            height: auto; 
            border-radius: 5px; 
            margin-top: 10px; 
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>เพิ่มจุดเช็คอิน</h2>
    <form action="add_checkpoint.php" method="post" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="ชื่อจุดเช็คอิน" required>
        <textarea name="description" placeholder="รายละเอียด" required></textarea>
        <input type="file" name="image" accept="image/*" required>
        <button type="submit" class="styled-button">เพิ่มจุดเช็คอิน</button>
    </form>
    <a href="admin.php" class="back-link"><i class="fas fa-arrow-left"></i> กลับ</a>
</div>

<?php $conn->close(); ?>

</body>
</html>
