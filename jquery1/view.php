<?php
session_start();
require_once "db.php";

if (!isset($_GET['profile_id'])) {
    die("Missing profile_id");
}

$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :pid");
$stmt->execute([':pid' => $_GET['profile_id']]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$profile) {
    die("Profile not found");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>View Profile - Rohit</title>
</head>
<body>
<h1>Profile Details</h1>
<p><strong>First Name:</strong> <?= htmlentities($profile['first_name']) ?></p>
<p><strong>Last Name:</strong> <?= htmlentities($profile['last_name']) ?></p>
<p><strong>Email:</strong> <?= htmlentities($profile['email']) ?></p>
<p><strong>Headline:</strong> <?= htmlentities($profile['headline']) ?></p>
<p><strong>Summary:</strong><br/> <?= htmlentities($profile['summary']) ?></p>
<p><a href="index.php">Back</a></p>
</body>
</html>