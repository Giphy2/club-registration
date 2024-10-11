<?php
require 'db_connect.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token is valid and not expired
    $sql = "SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            if ($new_password === $confirm_password) {
                // Hash the new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Get the email associated with the token
                $row = $result->fetch_assoc();
                $email = $row['email'];

                // Update the student's password
                $sql = "UPDATE students SET password = ? WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ss', $hashed_password, $email);
                $stmt->execute();

                // Delete the token from password_resets table
                $sql = "DELETE FROM password_resets WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('s', $email);
                $stmt->execute();

                echo "Your password has been successfully reset.";
            } else {
                echo "Passwords do not match.";
            }
        }
    } else {
        echo "This reset token is invalid or has expired.";
    }
}
?>

<!-- HTML Form to reset the password -->
<form action="reset_password.php?token=<?= htmlspecialchars($token) ?>" method="POST">
    <input type="password" name="new_password" placeholder="New Password" required>
    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
    <button type="submit">Reset Password</button>
</form>
