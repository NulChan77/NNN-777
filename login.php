<?php
session_start();

// ตรวจสอบการล็อกอิน
if (isset($_SESSION['logged_in'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: dashboard.php'); // เปลี่ยนไปยัง dashboard สำหรับ admin
    } else {
        header('Location: user_dashboard.php'); // เปลี่ยนไปยัง user_dashboard สำหรับผู้ใช้ทั่วไป
    }
    exit;
}

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'user_management');

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = ''; // ตัวแปรสำหรับเก็บข้อความผิดพลาด
$success_message = ''; // ตัวแปรสำหรับเก็บข้อความสำเร็จ

// ตรวจสอบการล็อกอินเมื่อผู้ใช้กดปุ่มเข้าสู่ระบบ
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username']; // เปลี่ยนเป็น username
    $password = $_POST['password'];

    // ค้นหาผู้ใช้ในฐานข้อมูล
    $sql = "SELECT * FROM users WHERE username = ?"; // เปลี่ยนเป็น username
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // ตรวจสอบรหัสผ่าน
        if (password_verify($password, $row['password'])) {
            // ถ้ารหัสผ่านถูกต้อง
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];

            // ส่งไปยังหน้า Dashboard ตามบทบาท
            $success_message = "ล็อกอินสำเร็จ!";
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        let countdown = 2; // เริ่มต้นนับถอยหลังที่ 2 วินาที
                        const timerElement = document.getElementById('timer');
                        timerElement.textContent = countdown;

                        const countdownInterval = setInterval(function() {
                            countdown--;
                            timerElement.textContent = countdown;
                            if (countdown === 0) {
                                clearInterval(countdownInterval);
                                window.location.href = '" . ($row['role'] === 'admin' ? 'dashboard.php' : 'user_dashboard.php') . "';
                            }
                        }, 1000); // ลดค่า countdown ทุก 1 วินาที
                    });
                  </script>";
        } else {
            $error_message = "คุณใส่รหัสผ่านผิด กรุณาใส่ใหม่"; 
        }
    } else {
        $error_message = "Username ไม่ถูกต้อง กรุณาใส่ใหม่"; 
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Login</title>
    <style>
        body {
            font-family: 'Itim', cursive;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #FFF;
            overflow: hidden;
        }

        .container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            position: relative;
            z-index: 1;
            text-align: center;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            max-width: 300px;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #007bff;
            border-radius: 5px;
            font-size: 16px;
        }

        input::placeholder {
            color: #999;
        }

        .icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #007bff;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .back-home {
            display: inline-block;
            padding: 10px 20px;
            background-color: #6c757d; /* สีเทา */
            color: white;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 15px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .back-home:hover {
            background-color: #5a6268; /* สีเทาเข้มขึ้นเมื่อ hover */
        }

        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .success-message {
            color: #28a745;
            margin-bottom: 15px;
            font-weight: bold;
        }

        p {
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="success-message">
                <?php echo $success_message; ?>
                <p><span id="timer"></span></p>
            </div>
        <?php endif; ?>
        <form method="POST">
            <div style="position: relative;">
                <i class="fas fa-user icon"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div style="position: relative;">
                <i class="fas fa-lock icon"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit">Login</button>
        </form>

        <p>Don't have an account? <a href="register.php">Register here</a></p>
        
        <a href="index.php" class="back-home">Back to Home</a>
    </div>
</body>
</html>
