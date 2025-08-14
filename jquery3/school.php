<?php
session_start();
require_once "db.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    return;
}

$stmt = $pdo->prepare("SELECT name FROM Institution WHERE name LIKE :prefix");
$stmt->execute([':prefix' => $_GET['term'].'%']);
$schools = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $schools[] = $row['name'];
}
echo json_encode($schools, JSON_PRETTY_PRINT);