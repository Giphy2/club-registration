<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
</head>
<body>
    <h2>Reset Your Password</h2>
    <p>Enter your email address and we'll send you a link to reset your password.</p>
    
    <form action="forgot_password.php" method="post">
        <label for="email">Email:</label>
        <input type="email" name="email" required><br><br>

        <input type="submit" value="Send Reset Link">
    </form>

    <p><a href="index.php">Back to Login</a></p>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "userdb";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $email = trim($_POST['email']);

    // Check if the email exists
    $sql = "SELECT * FROM students WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate a unique password reset token
        $token = bin2hex(random_bytes(32)); // 64 characters

        // Set expiration time for the token (e.g., 1 hour)
        $expires = date("U") + 3600;

        // Delete any existing tokens for this user
        $sql = "DELETE FROM password_resets WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        // Insert the token into the database
        $sql = "INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $email, $token, $expires);
        $stmt->execute();

        // Prepare password reset link
        $reset_link = "http://localhost/login_system/reset_password.php?token=" . $token;

        // Send password reset email (you can use a mail service like PHPMailer or the native PHP mail function)
        $to = $email;
        $subject = "Password Reset Request";
        $message = "You have requested a password reset. Click the link below to reset your password:\n\n";
        $message .= $reset_link . "\n\n";
        $message .= "If you did not request this password reset, please ignore this email.";
        $headers = "From: noreply@yourdomain.com";

        if (mail($to, $subject, $message, $headers)) {
            echo "A password reset link has been sent to your email address.";
        } else {
            echo "There was an error sending the email. Please try again later.";
        }
    } else {
        echo "No account found with that email address.";
    }

    $stmt->close();
    $conn->close();
}
