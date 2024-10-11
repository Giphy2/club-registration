<?php
session_start();
require 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit();
}

// Set the number of clubs per page
$clubs_per_page = 10;

// Determine the current page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);  // Ensure the page number is always positive

// Calculate the offset for the SQL query
$offset = ($page - 1) * $clubs_per_page;

// Fetch total number of clubs
$sql_total = "SELECT COUNT(*) as total_clubs FROM clubs";
$result_total = $conn->query($sql_total);
$total_clubs = $result_total->fetch_assoc()['total_clubs'];

// Calculate the total number of pages
$total_pages = ceil($total_clubs / $clubs_per_page);

// Fetch clubs for the current page with pagination
$sql = "SELECT * FROM clubs LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $clubs_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();
$clubs = $result->fetch_all(MYSQLI_ASSOC);

// Join club functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $club_id = $_POST['club_id'];
    $student_id = $_SESSION['user_id'];

    $sql = "INSERT INTO student_clubs (student_id, club_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $student_id, $club_id);
    $stmt->execute();

    header("Location: dashboard.php?joined=true");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Clubs</title>
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
        form {
            display: inline;
        }
        button {
            background-color: #00bfff;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .pagination a {
            margin: 0 5px;
            padding: 10px 15px;
            background-color: #00bfff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .pagination a:hover {
            background-color: #0073e6;
        }
        .pagination a.disabled {
            background-color: #cfcfcf;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Explore Clubs</h2>
        <ul class="club-list">
            <?php if (count($clubs) > 0): ?>
                <?php foreach ($clubs as $club): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($club['club_name']); ?></strong>
                        <p><?php echo htmlspecialchars($club['description']); ?></p>
                        <form action="explore_clubs.php?page=<?php echo $page; ?>" method="post">
                            <input type="hidden" name="club_id" value="<?php echo $club['id']; ?>">
                            <button type="submit">Join Club</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No clubs available.</li>
            <?php endif; ?>
        </ul>

        <!-- Pagination Links -->
        <div class="pagination">
            <!-- Previous Page Link -->
            <?php if ($page > 1): ?>
                <a href="explore_clubs.php?page=<?php echo $page - 1; ?>">&laquo; Previous</a>
            <?php else: ?>
                <a class="disabled">&laquo; Previous</a>
            <?php endif; ?>

            <!-- Page Number Links -->
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="explore_clubs.php?page=<?php echo $i; ?>" <?php if ($i == $page) echo 'style="font-weight: bold;"'; ?>><?php echo $i; ?></a>
            <?php endfor; ?>

            <!-- Next Page Link -->
            <?php if ($page < $total_pages): ?>
                <a href="explore_clubs.php?page=<?php echo $page + 1; ?>">Next &raquo;</a>
            <?php else: ?>
                <a class="disabled">Next &raquo;</a>
            <?php endif; ?>
        </div>

        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html>
