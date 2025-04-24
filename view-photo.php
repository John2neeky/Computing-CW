<?php
// Database connection
$servername = "localhost"; // The server hosting the database 
$username = "root"; // The username to access the database 
$password = ""; // The password for the database 
$database = "5114asst1"; // The name of the database you want to connect to

$conn = new mysqli($servername, $username, $password, $database);

// Checks connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get photo ID from URL and sanitize it
$photo_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

if (!$photo_id || !is_numeric($photo_id)) {
    die("Invalid photo ID.");
}

// Fetch photo from database
$sql = "SELECT * FROM photo WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $photo_id);
$stmt->execute();
$result = $stmt->get_result();
$photo = $result->fetch_assoc();

// Close database connection
$stmt->close();
$conn->close();

// If no photo found, show an error message
if (!$photo) {
    die("Photo not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($photo['title']); ?></title>
</head>
<body>

<h2><?php echo htmlspecialchars($photo['title']); ?></h2>
<img src="uploads/<?php echo htmlspecialchars($photo['photo_url']); ?>" alt="Photo" style="max-width: 100%; height: auto;">

<p><a href="index.php">Back to Gallery</a></p>

</body>
</html>
