<?php
session_start();
require_once "db.php";
require_once "util.php";

if (!isset($_SESSION['user_id'])) {
    die("Not logged in");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $validation = validateProfile();
    if ($validation !== true) {
        $_SESSION['error'] = $validation;
        header("Location: add.php");
        return;
    }

    $stmt = $pdo->prepare('INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary)
        VALUES (:uid, :fn, :ln, :em, :he, :su)');
    $stmt->execute([
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary']
    ]);
    $_SESSION['success'] = "Profile added";
    header("Location: index.php");
    return;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Profile - Rohit</title>
</head>
<body>
<h1>Adding Profile for <?= htmlentities($_SESSION['name']) ?></h1>
<?php flashMessages(); ?>
<form method="POST">
    First Name: <input type="text" name="first_name"><br/>
    Last Name: <input type="text" name="last_name"><br/>
    Email: <input type="text" name="email"><br/>
    Headline: <input type="text" name="headline"><br/>
    Summary:<br/>
    <textarea name="summary" rows="8" cols="80"></textarea><br/>
    <input type="submit" value="Add">
    <a href="index.php">Cancel</a>
</form>
</body>
</html>