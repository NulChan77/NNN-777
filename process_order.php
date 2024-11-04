<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    http_response_code(403);
    echo json_encode(['message' => 'Forbidden']);
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'user_management');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับข้อมูลจาก POST
$shippingAddress = $_POST['shippingAddress'];
$paymentMethod = $_POST['paymentMethod'];
$selectedProducts = json_decode($_POST['selectedProducts'], true);
$userId = $_SESSION['user_id']; // สมมติว่ามี user_id ใน session

foreach ($selectedProducts as $product) {
    $productId = $product['id'];
    $quantity = $product['quantity'];

    // คำนวณราคาสุทธิ
    $sql = "SELECT price FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $stmt->bind_result($price);
    $stmt->fetch();
    $stmt->close();

    $totalPrice = $price * $quantity;

    // บันทึกลงใน order_history
    $sql = "INSERT INTO order_history (user_id, product_id, quantity, total_price, shipping_address, payment_method) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iiissss', $userId, $productId, $quantity, $totalPrice, $shippingAddress, $paymentMethod);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
echo json_encode(['message' => 'Order processed successfully']);
?>
