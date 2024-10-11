<?php
session_start();
require 'db_connect.php';

// Set session timeout duration (e.g., 30 minutes)
$timeout_duration = 1800;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: index.php?timeout=true");
    exit();
}

$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity time
$user_id = $_SESSION['user_id'];

// Fetch active clubs
$sql = "SELECT clubs.club_name FROM student_clubs 
        JOIN clubs ON student_clubs.club_id = clubs.id 
        WHERE student_clubs.student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$active_clubs = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            background-color: #ffffff;
            font-family: Arial, sans-serif;
        }
        .container {
            width: 60%;
            margin: auto;
            padding: 20px;
        }
        h2 {
            color: #008000;
        }
        a {
            color: #00bfff;
        }
        .club-list {
            list-style: none;
        }
        .club-list li {
            background-color: #f0f8ff;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #00bfff;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
        <h3>Your Active Clubs:</h3>
        <?php if (count($active_clubs) > 0): ?>
            <ul class="club-list">
                <?php foreach ($active_clubs as $club): ?>
                    <li><?php echo htmlspecialchars($club['club_name']); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>You are not part of any clubs yet. <a href="explore_clubs.php">Explore available clubs</a></p>
        <?php endif; ?>
        <p><a href="logout.php">Logout</a></p>
    </div>
</body>
</html>
