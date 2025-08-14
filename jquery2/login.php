<?php
session_start();
require_once "db.php";
require_once "util.php";

$salt = 'XyZzy12*_';
if (isset($_POST['email']) && isset($_POST['pass'])) {
    $check = hash('md5', $salt . $_POST['pass']);
    $stmt = $pdo->prepare("SELECT user_id, name FROM users WHERE email = :em AND password = :pw");
    $stmt->execute([':em' => $_POST['email'], ':pw' => $check]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row !== false) {
        $_SESSION['name'] = $row['name'];
        $_SESSION['user_id'] = $row['user_id'];
        header("Location: index.php");
        return;
    } else {
        $_SESSION['error'] = "Incorrect email or password";
        header("Location: login.php");
        return;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login - Rohit</title>
<script>
function doValidate() {
    console.log('Validating...');
    try {
        let email = document.getElementById('email').value;
        let pw = document.getElementById('id_1723').value;
        if (!email || !pw) {
            alert("Both fields must be filled out");
            return false;
        }
        if (email.indexOf('@') === -1) {
            alert("Email address must contain @");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
}
</script>
</head>
<body>
<h1>Please Log In</h1>
<?php flashMessages(); ?>
<form method="POST">
    <label for="email">Email</label>
    <input type="text" name="email" id="email"><br/>
    <label for="id_1723">Password</label>
    <input type="password" name="pass" id="id_1723"><br/>
    <input type="submit" onclick="return doValidate();" value="Log In">
</form>
</body>
</html>