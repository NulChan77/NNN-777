<?php
// Database connection
// $conn = new mysqli('host', 'user', 'password', 'database');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section_title = $_POST['section_title'];
    // Update title in database
    $stmt = $conn->prepare("UPDATE sections SET title = ? WHERE id = 1");
    $stmt->bind_param("s", $section_title);
    $stmt->execute();
    $stmt->close();
}

// Redirect back to the main page
header("Location: index.php");
exit;
?>
