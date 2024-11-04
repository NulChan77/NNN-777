<?php
// เชื่อมต่อฐานข้อมูล
include 'db_connection.php';

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ดึงข้อมูลจุดเช็คอินทั้งหมด
$checkpoints_result = $conn->query("SELECT * FROM checkpoints ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการจุดเช็คอิน</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: center; /* ตั้งค่าให้เนื้อหาตรงกลาง */
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4caf50;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        td img {
            width: 100px; /* กำหนดความกว้างของภาพ */
            height: 75px; /* กำหนดความสูงของภาพ */
        }
        .edit-button, .delete-button {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            cursor: pointer;
        }
        .edit-button {
            background-color: #4caf50;
        }
        .delete-button {
            background-color: #f44336;
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
        /* กำหนดความกว้างของคอลัมน์รายละเอียด */
        .description-column {
            max-width: 200px; /* กำหนดความกว้างสูงสุด */
            overflow: hidden; /* ซ่อนเนื้อหาที่เกิน */
            text-overflow: ellipsis; /* แสดง "..." สำหรับข้อความที่ถูกซ่อน */
            white-space: nowrap; /* ไม่ให้ข้อความในคอลัมน์นี้ขึ้นบรรทัดใหม่ */
        }
    </style>
</head>
<body>

<h1>Checkpoint List</h1>

<table>
    <thead>
        <tr>
            <th>ภาพ</th>
            <th>ชื่อจุดเช็คอิน</th>
            <th class="description-column">รายละเอียด</th>
            <th>การดำเนินการ</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($checkpoint = $checkpoints_result->fetch_assoc()): ?>
            <tr>
                <td><img src="<?php echo $checkpoint['image_path'] ? $checkpoint['image_path'] : 'uploads/default.jpg'; ?>" alt="Checkpoint Image"></td>
                <td><?php echo htmlspecialchars($checkpoint['title']); ?></td>
                <td class="description-column"><?php echo htmlspecialchars($checkpoint['description']); ?></td>
                <td>
                    <a href="edit_checkpoint.php?id=<?php echo $checkpoint['id']; ?>" class="edit-button">แก้ไข</a>
                    <a href="delete_checkpoint.php?id=<?php echo $checkpoint['id']; ?>" class="delete-button" onclick="return confirm('คุณต้องการลบจุดเช็คอินนี้หรือไม่?')">ลบ</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<a href="admin.php" class="back-link"><i class="fas fa-arrow-left"></i> กลับ</a>

</body>
</html>
