<?php
// Database connection parameters
$host = 'localhost';      // Database host (usually localhost)
$db   = 'club_registration';  // Name of the database
$user = 'root';           // Database username (replace with your MySQL username)
$pass = '';               // Database password (replace with your MySQL password)

// Create a connection to MySQL database using MySQLi
$conn = new mysqli($host, $user, $pass, $db);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If you want to check the connection, you can uncomment this line
// echo "Server Connected successfully";
?>
