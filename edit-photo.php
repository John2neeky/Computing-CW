<?php
include 'config.php'; // Include database connection

// Ensure 'idphoto' is passed in the URL 
if (isset($_GET['idphoto'])) {
    $idphoto = $_GET['idphoto'];
    
    // Validate the ID (ensure it's an integer)
    if (!is_numeric($idphoto)) {
        die("Invalid ID");
    }
    
    // Query to fetch the photo data
    $query = "SELECT * FROM photo WHERE idphoto = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $idphoto);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $photo = $result->fetch_assoc();
    } else {
        die("Photo not found.");
    }
} else {
    die("No photo ID provided.");
}

// Handle form submission if POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $comment = trim($_POST["comment"]);
    $newPhoto = $_FILES["photo"];
    
    // Validate title
    if (empty($title)) {
        die("Title cannot be empty.");
    }
    
    // Check if a new photo was uploaded
    if ($newPhoto["size"] > 0) {
        // Allowed file types
        $allowedTypes = ["image/jpeg", "image/png", "image/gif"];
        if (!in_array($newPhoto["type"], $allowedTypes)) {
            die("Error: Only JPG, PNG, and GIF files are allowed.");
        }
        
        // Generate a unique filename
        $ext = pathinfo($newPhoto["name"], PATHINFO_EXTENSION);
        $filename = time() . "_" . uniqid() . "." . $ext;
        $targetPath = "uploads/" . $filename;
        
        // Move the new file to the uploads folder
        if (move_uploaded_file($newPhoto["tmp_name"], $targetPath)) {
            // Delete the old file from the server
            if (!empty($photo["imageurl"])) {
                unlink("uploads/" . $photo["imageurl"]);
            }
            
            // Update database with new image
            $updateQuery = "UPDATE photo SET title = ?, comment = ?, imageurl = ? WHERE idphoto = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("sssi", $title, $comment, $filename, $idphoto);
        } else {
            die("Error uploading new photo.");
        }
    } else {
        // No new photo uploaded, only update title and comment
        $updateQuery = "UPDATE photo SET title = ?, comment = ? WHERE idphoto = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ssi", $title, $comment, $idphoto);
    }
    
    // Execute the update query
    if ($updateStmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error updating photo: " . $updateStmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Photo</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Edit Photo</h1>
        
        <?php if (isset($photo)): ?>
            <form action="edit-photo.php?idphoto=<?php echo $photo['idphoto']; ?>" method="POST" enctype="multipart/form-data">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($photo['title']); ?>" required><br>


                <label for="photo">Upload New Photo:</label>
                <input type="file" id="photo" name="photo" accept="image/jpeg, image/png, image/gif"><br>

                <button type="submit">Update Photo</button>
            </form>
        <?php else: ?>
            <p>Photo not found.</p>
        <?php endif; ?>
        
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
