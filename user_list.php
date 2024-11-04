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

// ดึงข้อมูลผู้ใช้ทั้งหมด
$sql = "SELECT * FROM users";
$all_users_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <link rel="stylesheet" href="style.css">
    <style>
        button {
            padding: 8px 12px;
            margin-right: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .edit {
            background-color: #007bff;
            color: white;
        }
        .delete {
            background-color: #dc3545;
            color: white;
        }
        .add {
            background-color: #28a745;
            color: white;
        }
        .back-button {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User List</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th> <!-- เพิ่มคอลัมน์ Username -->
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $all_users_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td> <!-- แสดง Username -->
                        <td><?php echo htmlspecialchars($row['firstname']); ?></td>
                        <td><?php echo htmlspecialchars($row['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($row['gmail']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['role']); ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $row['id']; ?>">
                                <button class="edit">Edit</button>
                            </a>
                            <a href="edit_password.php?id=<?php echo $row['id']; ?>">
                                <button class="edit">Edit Password</button>
                            </a>
                            <p> </p>
                            <form method="POST" action="delete_user.php" style="display:inline;">
                                <input type="hidden" name="delete_user_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="add_user.php"><button class="add">Add User</button></a>
        <a href="dashboard.php"><button class="back-button">Back to Dashboard</button></a>
        <form method="POST" action="logout.php" style="display:inline;">
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>