<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $admin_username = $_POST['admin_username'];
    $admin_password = $_POST['admin_password'];

    $sql = "SELECT * FROM admins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $admin_username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($admin_password, $admin['password'])) {
        $_SESSION['admin_loggedin'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        header('Location: admin_dashboard.php');
        exit();
    } else {
        $error = "Incorrect admin username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login">
        <form action="admin_login.php" method="POST">
            <h1>Admin Login</h1>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>
            <label>Username</label>
            <input type="text" name="admin_username" required>
            <label>Password</label>
            <input type="password" name="admin_password" required>
            <button type="submit">Login</button>
            <p>Go back to <a href="login.php">Student Login</a></p>
        </form>
    </div>
</body>
</html>
