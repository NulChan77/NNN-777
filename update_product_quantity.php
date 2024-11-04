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

// ตรวจสอบว่ามีข้อมูล POST หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $products = $_POST['products'];

    foreach ($products as $product) {
        $id = $product['id'];
        $quantity = $product['quantity'];

        // อัปเดตจำนวนสินค้าในฐานข้อมูล
        $sql = "UPDATE products SET quantity = quantity - ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $quantity, $id);
        $stmt->execute();
    }

    echo "Order processed successfully.";
}

$conn->close();
?>
