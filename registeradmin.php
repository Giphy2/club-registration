<?php
require 'db_connect.php';

// Admin credentials
$admin_username = 'admin';
$admin_email = 'admin@example.com';
$admin_password = password_hash('password123', PASSWORD_DEFAULT); // Hash the password

// Insert the admin into the admins table
$sql = "INSERT INTO admins (username, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sss', $admin_username, $admin_email, $admin_password);

if ($stmt->execute()) {
    echo "Admin account created successfully!";
} else {
    echo "Error: " . $stmt->error;
}

$conn->close();
?>
