<?php
session_start();
require_once "pdo.php";

// Check if user is logged in
if (!isset($_SESSION['name'])) {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cancel'])) {
        header("Location: view.php");
        exit;
    }

    $make = trim($_POST['make'] ?? '');
    $year = trim($_POST['year'] ?? '');
    $mileage = trim($_POST['mileage'] ?? '');

    // Validate input
    if ($make === '' || $year === '' || $mileage === '') {
        $_SESSION['error'] = "All fields are required";
        header("Location: add.php");
        exit;
    }

    if (!is_numeric($year) || !is_numeric($mileage)) {
        $_SESSION['error'] = "Mileage and year must be numeric";
        header("Location: add.php");
        exit;
    }

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO autos (make, year, mileage) VALUES (:mk, :yr, :mi)");
    $stmt->execute([
        ':mk' => $make,
        ':yr' => $year,
        ':mi' => $mileage
    ]);

    $_SESSION['success'] = "Record inserted";
    header("Location: view.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Auto - <?= htmlentities($_SESSION['name']) ?></title>
</head>
<body>
    <h1>Adding Autos for <?= htmlentities($_SESSION['name']) ?></h1>

    <?php
    // Display error message
    if (isset($_SESSION['error'])) {
        echo '<p style="color:red;">' . htmlentities($_SESSION['error']) . '</p>';
        unset($_SESSION['error']);
    }
    ?>

    <form method="post">
        <label>Make: <input type="text" name="make"></label><br/>
        <label>Year: <input type="text" name="year"></label><br/>
        <label>Mileage: <input type="text" name="mileage"></label><br/>
        <input type="submit" value="Add">
        <input type="submit" name="cancel" value="Cancel">
    </form>
</body>
</html>