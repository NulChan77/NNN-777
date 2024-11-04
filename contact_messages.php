<?php
session_start();
require_once 'db_connection.php'; // เชื่อมต่อฐานข้อมูล

// เช็คว่ามีการลบข้อความหรือไม่
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // คำสั่งลบข้อความจากฐานข้อมูล
    $stmt = $conn->prepare("DELETE FROM contacts WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    // ส่งผู้ใช้กลับไปที่หน้า contact_messages.php พร้อมข้อความยืนยันการลบ
    header("Location: contact_messages.php?deleted=1");
    exit();
}

// ดึงข้อมูลข้อความที่ติดต่อเข้ามาจากฐานข้อมูล
$sql = "SELECT * FROM contacts ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <title>ข้อความที่ติดต่อ</title>
    <style>
        body {
            font-family: 'Itim', cursive;
            background-color: #f4f4f4;
            margin: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .message-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px 0;
        }
        .message {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }
        .message:last-child {
            border-bottom: none; /* ไม่ให้มีเส้นขอบสุดท้าย */
        }
        .message strong {
            color: #00796b; /* สีเน้นให้กับชื่อ */
        }
        .delete-button {
            background-color: #d9534f;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
        }
        .delete-button:hover {
            background-color: #c9302c;
        }
        .back-button {
    background-color: #007BFF; /* สีน้ำเงิน */
    color: white; /* สีข้อความ */
    border: none; /* ไม่มีกรอบ */
    border-radius: 5px; /* มุมโค้ง */
    padding: 10px 20px; /* ระยะห่างในปุ่ม */
    font-size: 16px; /* ขนาดฟอนต์ */
    cursor: pointer; /* เปลี่ยนเคอร์เซอร์เมื่อวางเมาส์ */
    transition: background-color 0.3s, transform 0.2s; /* การเปลี่ยนสีและขนาด */
}

.back-button:hover {
    background-color: #0056b3; /* สีน้ำเงินเข้มเมื่อเลื่อนเมาส์ */
    transform: scale(1.05); /* ขยายขนาดปุ่มเล็กน้อย */
}

    </style>
</head>
<body>

<h1>ข้อความที่ติดต่อ</h1>

<?php if (isset($_GET['deleted'])): ?>
    <div style="color: green;">ลบข้อความเรียบร้อยแล้ว!</div>
<?php endif; ?>

<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="message-container">
            <div class="message">
                <strong><?php echo htmlspecialchars($row['name']); ?></strong> <br>
                <span><?php echo htmlspecialchars($row['email']); ?></span> <br>
                <p><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
                <small><?php echo $row['created_at']; ?></small>
                <form action="contact_messages.php" method="GET" style="display:inline;">
                    <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" class="delete-button" onclick="return confirm('คุณแน่ใจหรือว่าต้องการลบข้อความนี้?');">ลบ</button>
                </form>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>ยังไม่มีข้อความที่ติดต่อเข้ามา</p>
<?php endif; ?>
<a href="dashboard.php"><button class="back-button">Back to Dashboard</button></a>
</body>
</html>
