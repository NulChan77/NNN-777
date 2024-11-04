<?php
// Database connection
// $conn = new mysqli('host', 'user', 'password', 'database');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image_id = $_POST['image_id'];
    $caption = $_POST['caption'];

    // Image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $filePath = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $filePath);

        // Update image path and caption in database
        $stmt = $conn->prepare("UPDATE images SET image_path = ?, caption = ? WHERE id = ?");
        $stmt->bind_param("ssi", $filePath, $caption, $image_id);
    } else {
        // Update caption only if no new image is uploaded
        $stmt = $conn->prepare("UPDATE images SET caption = ? WHERE id = ?");
        $stmt->bind_param("si", $caption, $image_id);
    }
    $stmt->execute();
    $stmt->close();
}

header("Location: index.php");
exit;
?>
