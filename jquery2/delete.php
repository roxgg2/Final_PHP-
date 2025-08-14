<?php
session_start();
require_once "db.php";
require_once "util.php";

if (!isset($_SESSION['user_id'])) {
    die("Not logged in");
}

if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header("Location: index.php");
    return;
}

$stmt = $pdo->prepare("SELECT first_name, last_name FROM Profile WHERE profile_id = :pid AND user_id = :uid");
$stmt->execute([
    ':pid' => $_GET['profile_id'],
    ':uid' => $_SESSION['user_id']
]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    $_SESSION['error'] = "Could not load profile";
    header("Location: index.php");
    return;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("DELETE FROM Profile WHERE profile_id = :pid AND user_id = :uid");
    $stmt->execute([
        ':pid' => $_POST['profile_id'],
        ':uid' => $_SESSION['user_id']
    ]);
    $_SESSION['success'] = "Profile deleted";
    header("Location: index.php");
    return;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Deleting Profile - Rohit</title>
</head>
<body>
<h1>Deleting Profile</h1>
<?php flashMessages(); ?>
<p><strong>First Name:</strong> <?= htmlentities($profile['first_name']) ?></p>
<p><strong>Last Name:</strong> <?= htmlentities($profile['last_name']) ?></p>
<form method="POST">
    <input type="hidden" name="profile_id" value="<?= $_GET['profile_id'] ?>">
    <input type="submit" value="Delete">
    <a href="index.php">Cancel</a>
</form>
</body>
</html>