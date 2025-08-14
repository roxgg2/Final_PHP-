<?php
session_start();
require_once "pdo.php";

// Check if user is logged in
if (!isset($_SESSION['name'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Autos Database - Rohit</title>
</head>
<body>
    <h1>Tracking Autos for <?= htmlentities($_SESSION['name']) ?></h1>

    <?php
    // Display success message
    if (isset($_SESSION['success'])) {
        echo '<p style="color:green;">' . htmlentities($_SESSION['success']) . '</p>';
        unset($_SESSION['success']);
    }
    ?>

    <table border="1">
        <tr><th>Make</th><th>Year</th><th>Mileage</th></tr>
        <?php
        $stmt = $pdo->query("SELECT make, year, mileage FROM autos");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr><td>" . htmlentities($row['make']) . "</td>";
            echo "<td>" . htmlentities($row['year']) . "</td>";
            echo "<td>" . htmlentities($row['mileage']) . "</td></tr>";
        }
        ?>
    </table>

    <p><a href="add.php">Add New</a> | <a href="logout.php">Logout</a></p>
</body>
</html>