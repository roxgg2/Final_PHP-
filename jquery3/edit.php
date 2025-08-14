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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $profileCheck = validateProfile();
    $entryCheck = validatePosEdu();

    if ($profileCheck !== true) {
        $_SESSION['error'] = $profileCheck;
        header("Location: edit.php?profile_id=" . $_POST['profile_id']);
        return;
    }

    if ($entryCheck !== true) {
        $_SESSION['error'] = $entryCheck;
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

    $stmt = $pdo->prepare("DELETE FROM Education WHERE profile_id=:pid");
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

    $rank = 1;
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['edu_year'.$i]) || !isset($_POST['edu_school'.$i])) continue;
        $school = $_POST['edu_school'.$i];
        $stmt = $pdo->prepare("SELECT institution_id FROM Institution WHERE name = :name");
        $stmt->execute([':name' => $school]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $inst_id = $row['institution_id'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO Institution (name) VALUES (:name)");
            $stmt->execute([':name' => $school]);
            $inst_id = $pdo->lastInsertId();
        }

        $stmt = $pdo->prepare("INSERT INTO Education (profile_id, institution_id, rank, year)
            VALUES (:pid, :iid, :rank, :year)");
        $stmt->execute([
            ':pid' => $_POST['profile_id'],
            ':iid' => $inst_id,
            ':rank' => $rank,
            ':year' => $_POST['edu_year'.$i]
        ]);
        $rank++;
    }

    $_SESSION['success'] = "Profile updated";
    header("Location: index.php");
    return;
}

$stmt = $pdo->prepare("SELECT * FROM Position WHERE profile_id = :pid ORDER BY rank");
$stmt->execute([':pid' => $_GET['profile_id']]);
$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT year, name FROM Education JOIN Institution ON Education.institution_id = Institution.institution_id WHERE profile_id = :pid ORDER BY rank");
$stmt->execute([':pid' => $_GET['profile_id']]);
$educations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Profile - Rohit</title>
<?php require_once "head.php"; ?>
</head>
<body>
<div class="container">
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

    <p>Education: <input type="button" id="addEdu" value="+"></p>
    <div id="edu_fields">
        <?php
        $countEdu = 0;
        foreach ($educations as $edu) {
            $countEdu++;
            echo '<div id="edu'.$countEdu.'">';
            echo '<p>Year: <input type="text" name="edu_year'.$countEdu.'" value="'.htmlentities($edu['year']).'" />';
            echo '<input type="button" value="-" onclick="$(\'#edu'.$countEdu.'\').remove();return false;"></p>';
            echo '<p>School: <input type="text" size="80" name="edu_school'.$countEdu.'" class="school" value="'.htmlentities($edu['name']).'" /></p>';
            echo '</div>';
        }
        ?>
    </div>

    <p>Position: <input type="button" id="addPos" value="+"></p>
    <div id="position_fields">
        <?php
        $countPos = 0;
        foreach ($positions as $pos) {
            $countPos++;
            echo '<div id="position'.$countPos.'">';
            echo '<p>Year: <input type="text" name="year'.$countPos.'" value="'.htmlentities($pos['year']).'" />';
            echo '<input type="button" value="-" onclick="$(\'#position'.$countPos.'\').remove();return false;"></p>';
            echo '<textarea name="desc'.$countPos.'" rows="8" cols="80">'.htmlentities($pos['description']).'</textarea>';
            echo '</div>';
        }
        ?>
    </div>

    <input type="submit" value="Save">
    <a href="index.php">Cancel</a>
</form>

<script>
let countEdu = <?= $countEdu ?>;
let countPos = <?= $countPos ?>;

$(document).ready(function() {
    $('#addEdu').click(function(event){
        event.preventDefault();
        if (countEdu >= 9) {
            alert("Maximum of nine education entries exceeded");
            return;
        }
        countEdu++;
        $('#edu_fields').append(
            '<div id="edu'+countEdu+'"> \
            <p>Year: <input type="text" name="edu_year'+countEdu+'" /> \
            <input type="button" value="-" onclick="$(\'#edu'+countEdu+'\').remove();return false;"></p> \
            <p>School: <input type="text" size="80" name="edu_school'+countEdu+'" class="school" value="" /></p>\
            </div>');
        $('.school').autocomplete({ source: "school.php" });
    });

    $('#addPos').click(function(event){
        event.preventDefault();
        if (countPos >= 9) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" /> \
            <input type="button" value="-" onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });

    $('.school').autocomplete({ source: "school.php" });
});
</script>
</div>
</body>
</html>