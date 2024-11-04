<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'user_management');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่ามีการส่ง user_id มาหรือไม่
if (!isset($_GET['id'])) {
    header('Location: user_list.php');
    exit;
}

$user_id = $_GET['id'];

// ดึงข้อมูลผู้ใช้ตาม ID
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// แก้ไขข้อมูลผู้ใช้
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username']; // รับ Username
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $gmail = $_POST['gmail'];
    $role = $_POST['role']; // รับค่าบทบาท
    $phone = $_POST['phone']; // รับหมายเลขโทรศัพท์

    $sql = "UPDATE users SET username = ?, firstname = ?, lastname = ?, gmail = ?, role = ?, phone = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $username, $firstname, $lastname, $gmail, $role, $phone, $user_id);

    if ($stmt->execute()) {
        header('Location: user_list.php');
        exit;
    } else {
        echo "Error updating information.";
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Edit User Information</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: auto;
        }
        input[type="text"],
        input[type="email"],
        select,
        button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit User Information</h2>
        <form method="POST">
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required placeholder="Username">
            <input type="text" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" required placeholder="First Name">
            <input type="text" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required placeholder="Last Name">
            <input type="email" name="gmail" value="<?php echo htmlspecialchars($user['gmail']); ?>" required placeholder="Email">
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" placeholder="Phone Number" required>

            <!-- Dropdown for selecting role -->
            <label for="role">Select Role:</label>
            <select name="role" id="role" required>
                <option value="user" <?php echo ($user['role'] === 'user') ? 'selected' : ''; ?>>User</option>
                <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
            </select>

            <button type="submit">Save Changes</button>
        </form>
        <a href="user_list.php"><button>Back to User List</button></a>
    </div>
</body>
</html>
