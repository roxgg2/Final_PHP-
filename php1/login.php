<?php
require_once "pdo.php";
$salt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1'; // password is php123

$failure = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['who']) || empty($_POST['pass'])) {
        $failure = "Email and password are required";
    } elseif (strpos($_POST['who'], '@') === false) {
        $failure = "Email must have an at-sign (@)";
    } else {
        $check = hash('md5', $salt . $_POST['pass']);
        if ($check == $stored_hash) {
            error_log("Login success " . $_POST['who']);
            header("Location: autos.php?name=" . urlencode($_POST['who']));
            return;
        } else {
            error_log("Login fail " . $_POST['who'] . " $check");
            $failure = "Incorrect password";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Rohit</title>
</head>
<body>
<h1>Please Log In</h1>
<?php
if ($failure !== false) {
    echo '<p style="color:red;">' . htmlentities($failure) . "</p>\n";
}
?>
<form method="POST">
    <label for="who">Email</label>
    <input type="text" name="who" id="who"><br/>
    <label for="pass">Password</label>
    <input type="text" name="pass" id="pass"><br/>
    <input type="submit" value="Log In">
</form>
</body>
</html>