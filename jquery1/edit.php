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

$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :pid AND user_id = :uid");
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
    $validation = validateProfile();
    if ($validation !== true) {
        $_SESSION['error'] = $validation;
        header("Location: edit.php?profile_id=" . $_POST['profile_id']);
        return;
    }

    $stmt = $pdo->prepare("UPDATE Profile SET first_name=:fn, last_name=:ln, email=:em,
        headline=:he, summary=:su WHERE profile_id=:pid AND user_id=:uid");
    $stmt->execute([
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'],
        ':pid' => $_POST['profile_id'],
        ':uid' => $_SESSION['user_id']
    ]);
    $_SESSION['success'] = "Profile updated";
    header("Location: index.php");
    return;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Profile - Rohit</title>
</head>
<body>
<h1>Editing Profile</h1>
<?php flashMessages(); ?>
<form method="POST">
    <input type="hidden" name="profile_id" value="<?= $_GET['profile_id'] ?>">
    First Name: <input type="text" name="first_name" value="<?= htmlentities($profile['first_name']) ?>"><br/>
    Last Name: <input type="text" name="last_name" value="<?= htmlentities($profile['last_name']) ?>"><br/>
    Email: <input type="text" name="email" value="<?= htmlentities($profile['email']) ?>"><br/>
    Headline: <input type="text" name="headline" value="<?= htmlentities($profile['headline']) ?>"><br/>
    Summary:<br/>
    <textarea name="summary" rows="8" cols="80"><?= htmlentities($profile['summary']) ?></textarea><br/>
    <input type="submit" value="Save">
    <a href="index.php">Cancel</a>
</form>
</body>
</html>