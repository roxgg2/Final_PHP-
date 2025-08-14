<?php
session_start();
require_once "db.php";
require_once "util.php";
?>

<!DOCTYPE html>
<html>
<head>
<title>Resume Registry - Rohit</title>
</head>
<body>
<h1>Resume Registry</h1>
<?php flashMessages(); ?>

<?php
$stmt = $pdo->query("SELECT profile_id, user_id, first_name, last_name, headline FROM Profile");
echo '<table border="1">';
echo "<tr><th>Name</th><th>Headline</th><th>Action</th></tr>";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr><td>";
    echo '<a href="view.php?profile_id=' . $row['profile_id'] . '">';
    echo htmlentities($row['first_name'] . ' ' . $row['last_name']);
    echo '</a></td><td>';
    echo htmlentities($row['headline']);
    echo '</td><td>';
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id']) {
        echo '<a href="edit.php?profile_id=' . $row['profile_id'] . '">Edit</a> ';
        echo '<a href="delete.php?profile_id=' . $row['profile_id'] . '">Delete</a>';
    }
    echo '</td></tr>';
}
echo "</table>\n";

if (!isset($_SESSION['user_id'])) {
    echo '<p><a href="login.php">Please log in</a></p>';
} else {
    echo '<p><a href="add.php">Add New Entry</a></p>';
    echo '<p><a href="logout.php">Logout</a></p>';
}
?>
</body>
</html>