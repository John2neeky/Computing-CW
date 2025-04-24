<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $file = $_FILES["photo"];
    $comment = trim($_POST["comment"]);
    
    if (empty($title)) {
        die("Title cannot be empty.");
    }
    
    $allowedTypes = ["image/jpeg", "image/png", "image/gif"];
    if (!in_array($file["type"], $allowedTypes)) {
        die("Error: Only JPG, PNG, and GIF files are allowed.");
    }
    
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }
    
    $ext = pathinfo($file["name"], PATHINFO_EXTENSION);
    $filename = time() . "_" . uniqid() . "." . $ext;
    $targetPath = "uploads/" . $filename;
    
    if (move_uploaded_file($file["tmp_name"], $targetPath)) {
        $stmt = $conn->prepare("INSERT INTO photo (title, imageurl, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $filename, $comment);
        
        if ($stmt->execute()) {
            header("Location: index.php");
            exit;
        } else {
            echo "Database Error: " . $stmt->error;
        }
    } else {
        echo "Error uploading file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Photo</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Add New Photo</h1>
        <form action="add-photo.php" method="POST" enctype="multipart/form-data">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required><br>

            <label for="photo">Upload Photo:</label>
            <input type="file" id="photo" name="photo" accept="image/jpeg, image/png, image/gif" required><br>

            

            <button type="submit">Upload Photo</button>
        </form>
        <br>
        <a href="index.php">Back to Gallery</a>
    </div>
</body>
</html>

<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f0f0f5; /* Light purple-gray background */
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.container {
    background: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 300px;
    text-align: center;
}

h1 {
    font-size: 22px;
    color: #6a0dad; /* Deep Purple */
}

form {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

label {
    font-weight: bold;
    text-align: left;
    color: #6a0dad; /* Purple */
}

input[type="text"],
input[type="file"] {
    width: 100%;
    padding: 8px;
    margin: 5px 0;
    border: 2px solid #6a0dad; /* Purple border */
    border-radius: 4px;
}

button {
    background-color: #228B22; /* Green */
    color: white;
    border: none;
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s ease;
    font-weight: bold;
}

button:hover {
    background-color: #176617; /* Darker Green */
}

a {
    display: inline-block;
    margin-top: 10px;
    text-decoration: none;
    color: #6a0dad; /* Purple */
    font-weight: bold;
}

a:hover {
    text-decoration: underline;
}

</style>

