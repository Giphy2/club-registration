<?php
session_start();

// Check if the admin is logged in, if not redirect to the login page
if (!isset($_SESSION['admin_loggedin'])) {
    header('Location: admin_login.php');
    exit();
}

// Include database connection
require 'db_connect.php';

// Query to fetch all student details
$sql = "SELECT * FROM students";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Display all student data in a table
    echo "<h1>Registered Students</h1>";
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Date Registered</th>
            </tr>";

    // Fetch and display each student row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row['id'] . "</td>
                <td>" . $row['username'] . "</td>
                <td>" . $row['email'] . "</td>
                <td>" . $row['created_at'] . "</td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "No students found.";
}

$conn->close();
?>
<a href="admin_logout.php">Logout</a>
