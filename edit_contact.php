<?php
include 'db_connection.php';

$address = $_POST['address'];
$opening_hours = $_POST['opening_hours'];

// อัปเดตข้อมูลในตาราง contact_info
$stmt = $conn->prepare("UPDATE contact_info SET address = ?, opening_hours = ? WHERE id = 1");
$stmt->bind_param("ss", $address, $opening_hours);
$stmt->execute();
$stmt->close();

header("Location: index.php");
?>
