<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Get the filename before deleting
    $result = $conn->query("SELECT filename FROM photo WHERE id = $id");
    $photo = $result->fetch_assoc();
    
    // Delete from database
    $conn->query("DELETE FROM photo WHERE id = $id");
    
    // Delete file from uploads folder
    if ($photo && file_exists("uploads/" . $photo['filename'])) {
        unlink("uploads/" . $photo['filename']);
    }
    
    header("Location: index.php");
}
?>