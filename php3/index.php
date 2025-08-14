<?php
session_start();
require_once "pdo.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rohit - Automobile Database</title>
</head>
<body>
<h1>Welcome to the Automobiles Database</h1>

<?php
// 🔒 If user is not logged in
if (!isset($_SESSION['name'])) {
    echo '<p>Please <a href="login.php">log in</a></p>';
    echo '<p><a href="add.php">Attempt to add data without logging in</a></p>';
    echo '</body></html>';
    exit(); // Stop the rest of the page from loading
}

// ✅ If user is logged in, show success message
if (isset($_SESSION['success'])) {
    echo '<p style="color:green">'.htmlentities($_SESSION['success'])."</p>\n";
    unset($_SESSION['success']);
}

// 📦 Fetch automobile records
$stmt = $pdo->query("SELECT * FROM autos1");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 📋 Display records
if (count($rows) == 0) {
    echo "<p>No rows found</p>";
} else {
    echo "<table border='1'>\n";
    echo "<tr><th>Make</th><th>Model</th><th>Year</th><th>Mileage</th><th>Action</th></tr>";
    foreach ($rows as $row) {
        echo "<tr><td>".htmlentities($row['make'])."</td>";
        echo "<td>".htmlentities($row['model'])."</td>";
        echo "<td>".htmlentities($row['year'])."</td>";
        echo "<td>".htmlentities($row['mileage'])."</td>";
        echo "<td>";
        echo "<a href='edit.php?autos_id=".htmlentities($row['autos_id'])."'>Edit</a> / ";
        echo "<a href='delete.php?autos_id=".htmlentities($row['autos_id'])."'>Delete</a>";
        echo "</td></tr>\n";
    }
    echo "</table>";
}
?>


<p><a href="add.php">Add New Entry</a></p>
<p><a href="logout.php">Logout</a></p>
</body>
</html>