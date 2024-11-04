<?php
session_start();

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'user_management');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$success_message = ""; // ตัวแปรสำหรับข้อความสำเร็จ
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $gmail = $_POST['gmail'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // ตรวจสอบความซ้ำกันของ username, gmail, phone
    $sql_check = "SELECT id FROM users WHERE (username = ? OR gmail = ? OR phone = ?) AND id != ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("sssi", $username, $gmail, $phone, $user_id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $errors[] = "Username, Email, or Phone already exists. Please use different information.";
    }

    // ตรวจสอบการยืนยันรหัสผ่าน
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        // อัปเดตข้อมูลในฐานข้อมูล
        $sql_update = "UPDATE users SET firstname = ?, lastname = ?, username = ?, gmail = ?, phone = ?, password = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssssssi", $firstname, $lastname, $username, $gmail, $phone, $password, $user_id);
        $stmt_update->execute();

        if ($stmt_update->affected_rows > 0) {
            $success_message = "Your profile has been updated successfully.";
            // ดึงข้อมูลผู้ใช้ที่อัปเดตล่าสุด
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc(); // อัปเดตข้อมูลผู้ใช้ที่แสดง
        } else {
            $errors[] = "No changes were made.";
        }

        $stmt_update->close();
    }

    $stmt_check->close();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Itim', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }
        .form-container {
            width: 500px;
            padding: 30px;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .error {
            color: red;
            font-size: 14px;
        }
        .success {
            color: green;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .btn {
            padding: 12px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .back-btn {
            background-color: #6c757d;
            color: white;
            margin-top: 10px;
        }
        .back-btn:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Edit Profile</h2>
        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php echo implode('<br>', $errors); ?>
            </div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="firstname">First Name</label>
                <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
            </div>
            <div class="form-group">
                <label for="lastname">Last Name</label>
                <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="form-group">
                <label for="gmail">Email</label>
                <input type="email" id="gmail" name="gmail" value="<?php echo htmlspecialchars($user['gmail']); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn">Save Changes</button>
        </form>
        <button onclick="history.back()" class="btn back-btn">Back</button> <!-- ปุ่มย้อนกลับ -->
    </div>
</body>
</html>
