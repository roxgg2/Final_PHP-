<?php
session_start();
require_once "db.php";
require_once "util.php";

if (!isset($_SESSION['user_id'])) {
    die("ACCESS DENIED");
}

if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header("Location: index.php");
    return;
}

$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :pid AND user_id = :uid");
$stmt->execute([':pid' => $_GET['profile_id'], ':uid' => $_SESSION['user_id']]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$profile) {
    $_SESSION['error'] = "Could not load profile";
    header("Location: index.php");
    return;
}

$stmt = $pdo->prepare("SELECT * FROM Position WHERE profile_id = :pid ORDER BY rank");
$stmt->execute([':pid' => $_GET['profile_id']]);
$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $profileCheck = validateProfile();
    $positionCheck = validatePos();

    if ($profileCheck !== true) {
        $_SESSION['error'] = $profileCheck;
        header("Location: edit.php?profile_id=" . $_POST['profile_id']);
        return;
    }

    if ($positionCheck !== true) {
        $_SESSION['error'] = $positionCheck;
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

    $stmt = $pdo->prepare("DELETE FROM Position WHERE profile_id=:pid");
    $stmt->execute([':pid' => $_POST['profile_id']]);

    $rank = 1;
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year'.$i]) || !isset($_POST['desc'.$i])) continue;
        $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description)
            VALUES (:pid, :rank, :year, :desc)');
        $stmt->execute([
            ':pid' => $_POST['profile_id'],
            ':rank' => $rank,
            ':year' => $_POST['year'.$i],
            ':desc' => $_POST['desc'.$i]
        ]);
        $rank++;
    }

    $_SESSION['success'] = "Profile updated";
    header("Location: index.php");
    return;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile - Rohit</title>
    <script>
        let countPos = <?= count($positions) ?>;

        function addPosition() {
            if (countPos >= 9) {
                alert("Maximum of nine position entries exceeded");
                return;
            }
            countPos++;
            const div = document.createElement("div");
            div.innerHTML = `
                <p>Year: <input type="text" name="year${countPos}" />
                <br>Description:<br>
                <textarea name="desc${countPos}" rows="4" cols="80"></textarea></p>
            `;
            document.getElementById("position_fields").appendChild(div);
        }
    </script>
</head>
<body>
    <h1>Edit Profile</h1>

    <?php
    if (isset($_SESSION['error'])) {
        echo '<p style="color:red;">' . htmlentities($_SESSION['error']) . '</p>';
        unset($_SESSION['error']);
    }
    ?>

    <form method="post">
        <input type="hidden" name="profile_id" value="<?= htmlentities($_GET['profile_id']) ?>">

        <p>First Name:
        <input type="text" name="first_name" value="<?= htmlentities($profile['first_name']) ?>"></p>

        <p>Last Name:
        <input type="text" name="last_name" value="<?= htmlentities($profile['last_name']) ?>"></p>

        <p>Email:
        <input type="text" name="email" value="<?= htmlentities($profile['email']) ?>"></p>

        <p>Headline:
        <input type="text" name="headline" value="<?= htmlentities($profile['headline']) ?>"></p>

        <p>Summary:<br>
        <textarea name="summary" rows="8" cols="80"><?= htmlentities($profile['summary']) ?></textarea></p>

        <p>Positions:</p>
        <div id="position_fields">
            <?php
            $countPos = 0;
            foreach ($positions as $position) {
                $countPos++;
                echo '<p>Year: <input type="text" name="year' . $countPos . '" value="' . htmlentities($position['year']) . '"/>';
                echo '<br>Description:<br><textarea name="desc' . $countPos . '" rows="4" cols="80">' . htmlentities($position['description']) . '</textarea></p>';
            }
            ?>
        </div>
        <p><button type="button" onclick="addPosition();">+ Add Position</button></p>

        <p><input type="submit" value="Save"/>
        <a href="index.php">Cancel</a></p>
    </form>
</body>
</html>