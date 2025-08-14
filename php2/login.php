<?php
session_start();
require_once "pdo.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['email']) || empty($_POST['pass'])) {
        $_SESSION['error'] = "Email and password are required";
        header("Location: login.php");
        return;
    }

    if (strpos($_POST['email'], '@') === false) {
        $_SESSION['error'] = "Email must have an at-sign (@)";
        header("Location: login.php");
        return;
    }

    // Password check (example salt and hash)
    $salt = 'XyZzy12*_';
    $stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1'; // password is php123
    $check = hash('md5', $salt . $_POST['pass']);

    if ($check == $stored_hash) {
        error_log("Login success " . $_POST['email']);
        $_SESSION['name'] = $_POST['email'];
        header("Location: view.php");
        return;
    } else {
        error_log("Login fail " . $_POST['email'] . " $check");
        $_SESSION['error'] = "Incorrect password";
        header("Location: login.php");
        return;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Rushikesh</title>
</head>
<body>
<h1>Please Log In</h1>
<?php
if (isset($_SESSION['error'])) {
    echo '<p style="color:red;">' . htmlentities($_SESSION['error']) . "</p>\n";
    unset($_SESSION['error']);
}
?>
<form method="POST">
    Email: <input type="text" name="email"><br/>
    Password: <input type="password" name="pass"><br/>
    <input type="submit" value="Log In">
</form>
</body>
</html>