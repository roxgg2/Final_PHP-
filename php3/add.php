<?php
session_start(); // ✅ Required to access session variables
require_once "pdo.php";

// 🔒 Block access if not logged in
if (!isset($_SESSION['name'])) {
    die("ACCESS DENIED");
}

// 🧾 Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (
        empty($_POST['make']) ||
        empty($_POST['model']) ||
        empty($_POST['year']) ||
        empty($_POST['mileage'])
    ) {
        $_SESSION['error'] = "All fields are required";
        header("Location: add.php");
        return;
    }

    if (!is_numeric($_POST['year']) || !is_numeric($_POST['mileage'])) {
        $_SESSION['error'] = "Year and mileage must be integers";
        header("Location: add.php");
        return;
    }

    // ✅ Insert into database
    $stmt = $pdo->prepare("INSERT INTO autos1 (make, model, year, mileage) VALUES (:mk, :md, :yr, :mi)");
    $stmt->execute([
        ':mk' => $_POST['make'],
        ':md' => $_POST['model'],
        ':yr' => $_POST['year'],
        ':mi' => $_POST['mileage']
    ]);

    $_SESSION['success'] = "Record added";
    header("Location: index.php");
    return;
}
?>

<!DOCTYPE html>
<html>
<head><title>Add Automobile-Rohit</title></head>
<body>
<h1>Tracking Automobiles for <?= htmlentities($_SESSION['name']) ?></h1>

<?php
if (isset($_SESSION['error'])) {
    echo '<p style="color:red">'.htmlentities($_SESSION['error'])."</p>\n";
    unset($_SESSION['error']);
}
?>

<form method="POST">
Make: <input type="text" name="make"><br/>
Model: <input type="text" name="model"><br/>
Year: <input type="text" name="year"><br/>
Mileage: <input type="text" name="mileage"><br/>
<input type="submit" value="Add">
<a href="index.php">Cancel</a>
</form>
</body>
</html>