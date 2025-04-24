<?php
include 'config.php'; 

// Fetch all photos from the database
$sql = "SELECT * FROM photo ORDER BY idphoto DESC";
$result = $conn->query($sql);

if ($result === false) {
    die("Error in query: " . $conn->error);
}

// Handle photo deletion if requested
if (isset($_GET['delete_idphoto'])) {
    $delete_idphoto = intval($_GET['delete_idphoto']);
    
    // Get the file name before deleting
    $stmt = $conn->prepare("SELECT imageurl FROM photo WHERE idphoto = ?");
    $stmt->bind_param("i", $delete_idphoto);
    $stmt->execute();
    $stmt->bind_result($imageurl);
    $stmt->fetch();
    $stmt->close();

    // Delete the photo from the database
    $stmt = $conn->prepare("DELETE FROM photo WHERE idphoto = ?");
    $stmt->bind_param("i", $delete_idphoto);
    if ($stmt->execute()) {
        // Delete the photo file from the server
        unlink("uploads/" . $imageurl);
        header("Location: index.php"); // Redirect back to gallery
        exit;
    } else {
        echo "Error deleting photo: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Photo Gallery</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Photo Gallery</h1>

        <!-- Link to the page where users can add a photo -->
        <a href="add-photo.php">Add New Photo</a>

        <div class="gallery">
            <?php
            // Check if there are any photos
            if ($result->num_rows > 0) {
                // Loop through each photo and display it
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="photo-item">';
                    echo '<img src="uploads/' . htmlspecialchars($row["imageurl"]) . '" alt="' . htmlspecialchars($row["title"]) . '">';
                    echo '<p>' . htmlspecialchars($row["title"]) . '</p>';
                    // Edit button
                    echo '<a href="edit-photo.php?idphoto=' . $row["idphoto"] . '" class="edit-btn">Edit</a>';
                    // Delete button
                    echo '<a href="?delete_idphoto=' . $row["idphoto"] . '" class="delete-btn" >Delete</a>';
                    echo '</div>';
                }
            } else {
                echo "<p>No photos uploaded yet.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>

<style> 
body {
    font-family: Arial, sans-serif;
    background-color: #f0f0f5; 
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
    width: 320px;
    text-align: center;
}

h1 {
    font-size: 22px;
    color: #6a0dad; 
}

.gallery {
    display: flex;
    flex-direction: column;
    gap: 15px;
    align-items: center;
}

.photo-item {
    width: 100%;
    border-radius: 8px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
    padding: 10px;
    background: #ffffff;
    text-align: center;
}

.photo-item img {
    width: 100%; 
    height: auto; 
    max-height: 250px; 
    object-fit: contain;
    border-radius: 8px;
    background-color: #e0e0e0; 
}

p {
    font-size: 16px;
    color: #333;
    margin-top: 8px;
}

.edit-btn, .delete-btn {
    display: inline-block;
    margin: 5px;
    padding: 8px 12px;
    border-radius: 4px;
    text-decoration: none;
    font-weight: bold;
    color: white;
    transition: background 0.3s ease;
}

.edit-btn {
    background-color: #6a0dad; 
}

.edit-btn:hover {
    background-color: #5b0ba3;
}

.delete-btn {
    background-color: #d9534f; 
}

.delete-btn:hover {
    background-color: #c9302c;
}

a {
    display: inline-block;
    margin-top: 10px;
    text-decoration: none;
    color: #6a0dad; 
    font-weight: bold;
}

a:hover {
    text-decoration: underline;
}
</style>


