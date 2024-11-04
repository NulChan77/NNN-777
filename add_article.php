<?php
include 'db_connect.php'; // Database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = 1; // Default user_id (update as needed)

    // Check if an image file is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Allowed file extensions
        $allowedfileExtensions = array('jpg', 'jpeg', 'png', 'gif');
        
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = 'uploads/';
            $dest_path = $uploadFileDir . $newFileName;

            // Move the uploaded file
            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                // Insert article data including the image
                $sql = "INSERT INTO articles (title, content, image, user_id) VALUES (:title, :content, :image, :user_id)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['title' => $title, 'content' => $content, 'image' => $newFileName, 'user_id' => $user_id]);

                // Redirect to articles page after successful addition
                header("Location: article.php");
                exit(); // Important: Exit after redirect
            } else {
                echo 'There was an error moving the uploaded file.';
            }
        } else {
            echo 'Upload failed. Allowed file types: ' . implode(', ', $allowedfileExtensions);
        }
    } else {
        // Save article without an image
        $sql = "INSERT INTO articles (title, content, user_id) VALUES (:title, :content, :user_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['title' => $title, 'content' => $content, 'user_id' => $user_id]);

        // Redirect to articles page after successful addition
        header("Location: article.php");
        exit(); // Important: Exit after redirect
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Article</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 56px;
        }
        .container {
            padding: 20px;
        }
        .form-label {
            font-weight: bold;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <a class="navbar-brand" href="#">โปรโมทสถานที่ท่องเที่ยว</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="article.php">Articles</a></li>
                <li class="nav-item"><a class="nav-link" href="add_article.php">Add Article</a></li>
                <li class="nav-item"><a class="nav-link" href="search.php">Search</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <h1>Add New Article</h1>
        <form action="add_article.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title" class="form-label">Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="content" class="form-label">Content</label>
                <textarea name="content" class="form-control" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="image" class="form-label">Upload Image</label>
                <input type="file" name="image" class="form-control-file" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Add Article</button>
        </form>
        <a href="article.php" class="btn btn-secondary mt-3">Back to Articles</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
