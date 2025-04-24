
<?php
// Database connection details
$servername = "localhost";  // The server hosting the database 
$username = "root";         // The username to access the database 
$password = "";             // The password for the database 
$dbname = "5114asst1";      // The name of the database you want to connect to

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    // If there is an error, stop the script and show the error message
    die("Connection failed: " . $conn->connect_error);
}
?>