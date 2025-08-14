<?php
require_once "pdo.php";

if (!isset($_GET['name']) || strlen($_GET['name']) < 1) {
    die("Name parameter missing");
}

$message = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['logout'])) {
        header("Location: index.php");
        return;
    }

    if (empty($_POST['make'])) {
        $message = "Make is required";
    } elseif (!is_numeric($_POST['year']) || !is_numeric($_POST['mileage'])) {
        $message = "Mileage and year must be numeric";
    } else {
        $stmt = $pdo->prepare("INSERT INTO autos (make, year, mileage) VALUES (:mk, :yr, :mi)");
        $stmt->execute([
            ':mk' => $_POST['make'],
            ':yr' => $_POST['year'],
            ':mi' => $_POST['mileage']
        ]);
        $message = "Record inserted";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Automobiles - Rohit</title>
</head>
<body>
<h1>Tracking Autos for <?= htmlentities($_GET['name']) ?></h1>

<?php
if ($message !== false) {
    echo '<p style="color:' . ($message == "Record inserted" ? "green" : "red") . ';">' . htmlentities($message) . "</p>\n";
}
?>

<form method="POST">
    <label for="make">Make</label>
    <input type="text" name="make" id="make"><br/>
    <label for="year">Year</label>
    <input type="text" name="year" id="year"><br/>
    <label for="mileage">Mileage</label>
    <input type="text" name="mileage" id="mileage"><br/>
    <input type="submit" value="Add">
    <input type="submit" name="logout" value="Logout">
</form>

<h2>Automobiles</h2>
<ul>
<?php
$stmt = $pdo->query("SELECT make, year, mileage FROM autos");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<li>" . htmlentities($row['year']) . " " . htmlentities($row['make']) . " / " . htmlentities($row['mileage']) . "</li>\n";
}
?>
</ul>
</body>
</html>