<?php
session_start();
require_once "db.php";

// Redirect if profile_id is missing
if (!isset($_GET['profile_id']) || strlen($_GET['profile_id']) < 1) {
    $_SESSION['error'] = "Missing profile_id";
    header("Location: index.php");
    return;
}

// Fetch profile
$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :pid");
$stmt->execute([':pid' => $_GET['profile_id']]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    $_SESSION['error'] = "Profile not found";
    header("Location: index.php");
    return;
}

// Fetch education entries
$stmt = $pdo->prepare("
    SELECT year, name 
    FROM Education 
    JOIN Institution ON Education.institution_id = Institution.institution_id 
    WHERE profile_id = :pid 
    ORDER BY rank
");
$stmt->execute([':pid' => $_GET['profile_id']]);
$educations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch position entries
$stmt = $pdo->prepare("
    SELECT year, description 
    FROM Position 
    WHERE profile_id = :pid 
    ORDER BY rank
");
$stmt->execute([':pid' => $_GET['profile_id']]);
$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    <?php if (count($educations) > 0): ?>
        <p><strong>Education:</strong></p>
        <ul>
            <?php foreach ($educations as $edu): ?>
                <li><?= htmlentities($edu['year']) ?>: <?= htmlentities($edu['name']) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p><em>No education entries found.</em></p>
    <?php endif; ?>

    <?php if (count($positions) > 0): ?>
        <p><strong>Positions:</strong></p>
        <ul>
            <?php foreach ($positions as $pos): ?>
                <li><?= htmlentities($pos['year']) ?>: <?= htmlentities($pos['description']) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p><em>No position entries found.</em></p>
    <?php endif; ?>

    <p><a href="index.php">Back</a></p>
</body>
</html>