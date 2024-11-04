<?php
session_start();
if (isset($_SESSION['logged_in'])) {
    header('Location: dashboard.php');
    exit;
}

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'user_management');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success_message = ''; // ตัวแปรเก็บข้อความสำเร็จ

// Registration function
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $gmail = $_POST['gmail'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check password length
    if (strlen($password) < 8) {
        echo "Error: Password must be at least 8 characters.";
        exit;
    }

    // SQL to add a new user
    $sql = "INSERT INTO users (firstname, lastname, username, gmail, password, phone) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error preparing the statement: " . $conn->error);
    }

    $stmt->bind_param("ssssss", $firstname, $lastname, $username, $gmail, $hashed_password, $phone);

    if ($stmt->execute()) {
        $success_message = "สมัครเรียบร้อยแล้ว"; // ข้อความสำเร็จ
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    let countdown = 5; // เริ่มต้นนับถอยหลังที่ 5 วินาที
                    const timerElement = document.getElementById('timer'); 
                    timerElement.textContent = countdown;
                    
                    const countdownInterval = setInterval(function() {
                        countdown--;
                        timerElement.textContent = countdown;
                        if (countdown === 0) {
                            clearInterval(countdownInterval);
                            window.location.href = 'login.php';
                        }
                    }, 1000); // ลดค่า countdown ทุก 1 วินาที
                });
              </script>";
    } else {
        if ($stmt->errno == 1062) {
            echo "Error: This username, email, or phone number is already registered.";
        } else {
            echo "Error: " . $stmt->error;
        }
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
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Register</h2>

        <?php if ($success_message): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> <!-- ไอคอนติ๊กถูก -->
                <?php echo $success_message; ?>
                <p>กำลังเปลี่ยนหน้าในอีก <span id="timer">5</span> วินาที...</p>
            </div>
        <?php endif; ?>

        <form method="POST" onsubmit="return validateForm()">
        <form method="POST" onsubmit="return validateForm()">
        <form method="POST" onsubmit="return validateForm()">
    <div class="input-container">
        <label>
            <span class="icon"><i class="fas fa-user"></i></span>
            <input type="text" name="firstname" placeholder="First Name" required pattern="[A-Za-z]+" title="Only English letters are allowed">
        </label>
    </div>
    <div class="input-container">
        <label>
            <span class="icon"><i class="fas fa-user"></i></span>
            <input type="text" name="lastname" placeholder="Last Name" required pattern="[A-Za-z]+" title="Only English letters are allowed">
        </label>
    </div>
    <div class="input-container">
        <label>
            <span class="icon"><i class="fas fa-user-circle"></i></span>
            <input type="text" name="username" placeholder="Username" required pattern="[A-Za-z0-9_]+" title="Only letters, numbers, and underscores are allowed">
        </label>
    </div>
    <div class="input-container">
        <label>
            <span class="icon"><i class="fas fa-envelope"></i></span>
            <input type="email" name="gmail" placeholder="Email" required>
        </label>
    </div>
    <div class="input-container">
        <label>
            <span class="icon"><i class="fas fa-phone"></i></span>
            <input type="text" name="phone" placeholder="Phone Number" required pattern="\d{10}" title="Please enter a valid 10-digit phone number">
        </label>
    </div>
    <div class="input-container">
        <label>
            <span class="icon"><i class="fas fa-lock"></i></span>
            <input type="password" name="password" placeholder="Password" required minlength="8">
        </label>
    </div>
    <div class="input-container">
        <label>
            <span class="icon"><i class="fas fa-lock"></i></span>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        </label>
    </div>
    <button type="submit">Register</button>
</form>


        <a href="login.php"><button class="back-button">Back to Login</button></a>
    </div>

    <script>
        function validateForm() {
            const password = document.querySelector('input[name="password"]');
            const confirmPassword = document.querySelector('input[name="confirm_password"]');

            if (password.value !== confirmPassword.value) {
                alert("Passwords do not match. Please try again.");
                return false;
            }
            return true;
        }
    </script>

    <style>
body {
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    background: #FFFFFF;
}

.container {
    background-color: white;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 400px;
    position: relative;
    z-index: 1;
}

.input-container {
    position: relative;
    margin: 10px 0;
}

.input-container label {
    display: flex;
    align-items: center;
}

.input-container input {
    width: 100%;
    padding: 15px;
    margin-left: 10px;
    border: 1px solid #007BFF; /* กรอบเป็นสีน้ำเงิน */
    border-radius: 5px;
    font-size: 16px;
}

.input-container .icon {
    color: #007BFF; /* สีไอคอนน้ำเงิน */
    font-size: 18px;
    margin-right: 10px;
}

button {
    padding: 10px 20px;
    background-color: #007BFF; /* ปุ่มเป็นสีน้ำเงิน */
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

button:hover {
    background-color: #0056b3; /* สีปุ่มเมื่อ hover */
}

.back-button {
    background-color: #6c757d; /* ปุ่มกลับเป็นสีเทา */
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    width: 100%;
    transition: background-color 0.3s, transform 0.2s;
}

.back-button:hover {
    background-color: #5a6268; /* สีปุ่มกลับเมื่อ hover */
}

.success-message {
    color: #28a745; /* สีข้อความสำเร็จ */
    font-size: 18px;
    margin-bottom: 15px;
    font-weight: bold;
    display: flex;
    align-items: center;
    flex-direction: column;
}

.success-message i {
    margin-right: 8px;
    font-size: 22px;
}



    </style>
</body>
</html>
