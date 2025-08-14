<?php
session_start(); // ✅ This is the missing piece!
require_once "pdo.php";

$salt = 'XyZzy12*_';
$stored_hash = hash('md5', $salt . 'php123'); // password is php123

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['email']) || empty($_POST['pass'])) {
        $_SESSION['error'] = "User name and password are required";
        header("Location: login.php");
        return;
    } elseif (hash('md5', $salt . $_POST['pass']) !== $stored_hash) {
        $_SESSION['error'] = "Incorrect password";
        header("Location: login.php");
        return;
    } else {
        $_SESSION['name'] = $_POST['email'];
        $_SESSION['success'] = "Logged in successfully";
        header("Location: index.php");
        return;
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Your Name - Rohit</title></head>
<body>
<h1>Please Log In</h1>
<?php
if (isset($_SESSION['error'])) {
    echo '<p style="color:red">'.htmlentities($_SESSION['error'])."</p>\n";
    unset($_SESSION['error']);
}
?>
<form method="POST">
User Name <input type="text" name="email"><br/>
Password <input type="password" name="pass"><br/>
<input type="submit" value="Log In">
</form>
</body>
</html>