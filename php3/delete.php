<?php
require_once "pdo.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['name'])) {
    die("ACCESS DENIED");
}

if (!isset($_GET['autos_id'])) {
    die("Missing autos_id");
}

$autos_id = $_GET['autos_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete'])) {
        $stmt = $pdo->prepare("DELETE FROM autos1 WHERE autos_id = :id");
        $stmt->execute([':id' => $autos_id]);
        $_SESSION['success'] = "Record deleted";
        header("Location: index.php");
        return;
    } elseif (isset($_POST['cancel'])) {
        header("Location: index.php");
        return;
    }
}

// Fetch make for display
$stmt = $pdo->prepare("SELECT make FROM autos1 WHERE autos_id = :id");
$stmt->execute([':id' => $autos_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    die("Record not found");
}
?>

<!DOCTYPE html>
<html>
<head><title>Delete Confirmation-Rohit</title></head>
<body>
<p><strong>Confirm: Deleting <?= htmlentities($row['make']) ?></strong></p>

<form method="post">
    <input type="submit" name="delete" value="Delete">
    <input type="submit" name="cancel" value="Cancel">
</form>
</body>
</html>